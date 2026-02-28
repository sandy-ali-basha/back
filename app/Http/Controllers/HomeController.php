<?php

namespace App\Http\Controllers;

use App\Http\Data\AddSlideData;
use App\Http\Data\UpdateHomeSettingsData;
use App\Http\Data\UpdateSettingsData;
use App\Http\Data\UpdateSlideData;
use App\Http\Resources\HomeSlideCollection;
use App\Http\Resources\SettingCollection;
use App\Http\Resources\SettingResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\Setting;
use App\Services\SettingService;
use App\Models\HomeSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function getHomeSlides() 
    {
        $settings        = $this->settingService->getHomeSlides();
        $resource = new HomeSlideCollection($settings);
        $response      = new SuccessResponse($resource, Response::HTTP_OK);

        return response()->success($response);
    }   

    public function deleteHomeSlide(Request $requset, $id) {
        $setting = Setting::findOrFail($id);
        $setting->deleteMedia($setting->getMedia('image')[0]);
        $setting->delete();
        $response = new SuccessResponse("Home Slide is deleted", Response::HTTP_OK);
        return response()->success($response);

    }
    public function editHomeSlide(Request $requset, $id) {
        $data = UpdateSlideData::from($requset);
        
        try {
            DB::beginTransaction();
            $settings = $this->settingService->updateSlidesSettings($data, false);
            if (count($settings) < 1) {
                $response = new ErrorResponse("No Settings were found.", Response::HTTP_UNPROCESSABLE_ENTITY);
                return response()->error($response);
            }
            DB::commit();
            $settingsCollection          = new HomeSlideCollection($settings);
            $response               = new SuccessResponse($settingsCollection, Response::HTTP_OK);

            return response()->success($response);
        } catch (\Exception $e) {
            
            DB::rollBack();
            Log::error($e->getMessage());
            $response = new ErrorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }
    }
    public function addHomeSlide(Request $requset) {
        $data = AddSlideData::from($requset);
        $data->name = 'home.slides';
        try {
            DB::beginTransaction();
            $settings = $this->settingService->updateSlidesSettings($data);
            if (count($settings) < 1) {
                $response = new ErrorResponse("No Settings were found.", Response::HTTP_UNPROCESSABLE_ENTITY);
                return response()->error($response);
            }
            DB::commit();
            $settingsCollection          = new HomeSlideCollection($settings);
            $response               = new SuccessResponse($settingsCollection, Response::HTTP_OK);

            return response()->success($response);
        } catch (\Exception $e) {
            
            DB::rollBack();
            Log::error($e->getMessage());
            $response = new ErrorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }
    }

    public function update(Request $request)
    {
        $data                   = UpdateHomeSettingsData::from($request);
        try {
            DB::beginTransaction();
            $settings                  = $this->settingService->updateHomeSettings($data);
            if (count($settings) < 1) {
                $response = new ErrorResponse("No Settings were found.", Response::HTTP_UNPROCESSABLE_ENTITY);
                return response()->error($response);
            }
            DB::commit();
            $settingsCollection          = new SettingCollection($settings);
            $response               = new SuccessResponse($settingsCollection, Response::HTTP_OK);

            return response()->success($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $response = new ErrorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }
    }

    public function index()
    {
        $settings        = $this->settingService->getHomeSettings();
        $brandResource = new SettingCollection($settings);
        $response      = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }
  public function getDashHomeTabs()
{
    $collection = $this->settingService->getHomeTabs();

    $result = $collection->map(function ($item) {
        if ($item['type'] === 'section') {
            return [
                'type'  => 'section',
                'order' => $item['order'],
                'data'  => [
                    'id'          => $item['data']->id,
                    'type'        => $item['data']->type,
                    'active' => $item['data']->active,
                    'title'       => [
                        'en' => $item['data']->title_en,
                        'ar' => $item['data']->title_ar,
                        'kr' => $item['data']->title_kr,
                    ],
                    'description' => [
                        'en' => $item['data']->description_en,
                        'ar' => $item['data']->description_ar,
                        'kr' => $item['data']->description_kr,
                    ],
                    'items' => $item['data']->items->map(function ($subItem) {
                        return [
                            'id'          => $subItem->id,
                            'image'       => $subItem->image,
                            'cta'         => $subItem->cta_link,
                            'title'       => [
                                'en' => $subItem->title_en,
                                'ar' => $subItem->title_ar,
                                'kr' => $subItem->title_kr,
                            ],
                            'description' => [
                                'en' => $subItem->description_en,
                                'ar' => $subItem->description_ar,
                                'kr' => $subItem->description_kr,
                            ],
                        ];
                    }),
                ],
            ];
        }

        if ($item['type'] === 'setting') {
            $options = $item['data']->options ?? '{}';
           
            return [
                'type'  => 'setting',
                'order' => $item['order'],
                'data'  => [
                    'id'      => $item['data']->id,
                    'name'    => $item['data']->name,
                    'active' => $item['data']->active,
                    'value'   => $item['data']->value,
                    'options' => $options ?: ['type' => 'json'],
                    'image'   => $item['data']->image ?? null,
                ],
            ];
           
        }

        // لو في نوع غير معروف يرجع null (لاحقاً ممكن نحذفه)
        return null;
    })->filter()->sortBy('order')->values(); // filter لحذف null

    return response()->success(new SuccessResponse($result, 200));
}
    public function updateOrder(Request $request, $id)
    {
        // فاليديشن للـ body: order لازم يكون رقم و type لازم يكون section أو setting
        $validated = $request->validate([
            'order' => 'required|integer|min:1',
            'type'  => 'required|in:section,setting',
        ]);
    
        $order = $validated['order'];
        $type  = $validated['type'];
    
        // حسب النوع نحدد الجدول المناسب
        if ($type === 'section') {
            $item = HomeSection::find($id);
        } else { // type === 'setting'
            $item = Setting::find($id);
        }
    
        if (!$item) {
        return response()->success(new SuccessResponse(null, 404, 'Item not found'));
        }
    
        // نحدث قيمة order
        $item->order = $order;
        $item->save();
    
       return response()->success(new SuccessResponse($item, 200, 'Order updated successfully'));

    }

    public function toggleStatus(Request $request, $id)
    {
        // فاليديشين للـ type
        $request->validate([
            'type' => 'required|in:section,setting',
        ]);
    
        $type = $request->input('type');
    
        // تحديد الجدول حسب النوع
        if ($type === 'section') {
            $item = HomeSection::find($id);
        } elseif ($type === 'setting') {
            $item = Setting::find($id);
        }
        if (!$item) {
          return response()->success(new SuccessResponse("ID is not found", 404, 'Item not found'));
        }
    
        // قلب الحالة بين 0 و 1
        $item->active = $item->active ? 0 : 1;
        $item->save();
       return response()->success(new SuccessResponse($item, 200, 'updated successfully'));

}

public function getFreeShipping()
{
    $free_shipping = Setting::find(79);

    if (!$free_shipping) {
        return response()->success(
            new SuccessResponse(null, 404, 'Free shipping setting not found')
        );
    }

    return response()->success(
        new SuccessResponse([
            'free_shipping_limit' => (int) $free_shipping->value
        ], 200, 'Success')
    );
}

   public function updateFreeShipping(Request $request)
{
    $request->validate([
        'limit' => 'required|integer',
    ]);

    $free_shipping = Setting::find(79);

    if (!$free_shipping) {
        return response()->success(
            new SuccessResponse(null, 404, 'Please set a free limit in your settings table at row with id: 79')
        );
    }

    $free_shipping->value = $request->limit;
    $free_shipping->save();

    return response()->success(
        new SuccessResponse([
            'free_shipping_limit' => $free_shipping->value
        ], 200, 'Updated successfully')
    );
}
}
