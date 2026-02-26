<?php

namespace App\Http\Controllers;

use App\Http\Resources\HomeSectionCollection;
use App\Http\Resources\HomeSectionResource;
use App\Http\Resources\NavBarCollection;
use App\Http\Resources\NavBarResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\HomeSection;
use App\Models\HomeSectionItem;
use App\Models\NavBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\HomeSectionItemResource;

class HomePageController extends Controller
{


    public function index()
    {
        $sections = HomeSection::with('items')->get();
        $resource = new HomeSectionCollection($sections);
        return response()->success(new SuccessResponse($resource, Response::HTTP_OK));
    }

    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required|string',
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'title_kr' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_kr' => 'nullable|string',
            'items' => 'required|array',
            'items.*.image' => 'required|image',
            'items.*.title_en' => 'required|string',
            'items.*.title_ar' => 'required|string',
            'items.*.title_kr' => 'required|string',
            'items.*.description_en' => 'nullable|string',
            'items.*.description_ar' => 'nullable|string',
            'items.*.description_kr' => 'nullable|string',
            'items.*.cta_link' => 'nullable|url',
            'items.*.video_en' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'items.*.video_ar' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'items.*.video_kr' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
        ]);

        $section = HomeSection::create($request->only([
            'type', 
            'title_en','title_ar','title_kr', 
            'description_en','description_ar','description_kr'
        ]));

        foreach ($request->items as $item) {
            $imagePath = $item['image']->store('home', 'home_storage');
            $itemData = [
                'image' => $imagePath,
                'cta_link' => $item['cta_link'],
                'title_en' => $item['title_en'],
                'title_ar' => $item['title_ar'],
                'title_kr' => $item['title_kr'],
                'description_en' => $item['description_en'],
                'description_ar' => $item['description_ar'],
                'description_kr' => $item['description_kr'],
            ];

            if ($section->id === 4) {
                $itemData['video_en'] = isset($item['video_en']) ? $item['video_en']->store('home', 'home_storage') : null;
                $itemData['video_ar'] = isset($item['video_ar']) ? $item['video_ar']->store('home', 'home_storage') : null;
                $itemData['video_kr'] = isset($item['video_kr']) ? $item['video_kr']->store('home', 'home_storage') : null;
            }

            $section->items()->create($itemData);
        }

        $resource = new HomeSectionResource($section->load('items'));
        return response()->success(new SuccessResponse($resource, Response::HTTP_CREATED));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'title_kr' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_kr' => 'nullable|string',
            'items' => 'sometimes|array',
            'items.*.id' => 'nullable|integer|exists:home_section_items,id',
            'items.*.image' => 'nullable|image',
            'items.*.cta_link' => 'nullable|url',
            'items.*.video_en' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'items.*.video_ar' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'items.*.video_kr' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'items.*.title_en' => 'required_with:items|string',
            'items.*.title_ar' => 'required|string',
            'items.*.title_kr' => 'required|string',
            'items.*.description_en' => 'nullable|string',
            'items.*.description_ar' => 'nullable|string',
            'items.*.description_kr' => 'nullable|string',
        ]);

        $section = HomeSection::find($id);
        if (!$section) {
            return response()->error(new ErrorResponse('section not found', Response::HTTP_NOT_FOUND));
        }

        $section->update($request->only([
            'type', 
            'title_en','title_ar','title_kr', 
            'description_en','description_ar','description_kr'
        ]));

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                if (isset($item['id'])) {
                    $sectionItem = HomeSectionItem::findOrFail($item['id']);
                    $updateData = [
                        'cta_link' => $item['cta_link'],
                        'title_en' => $item['title_en'],
                        'title_ar' => $item['title_ar'],
                        'title_kr' => $item['title_kr'],
                        'description_en' => $item['description_en'],
                        'description_ar' => $item['description_ar'],
                        'description_kr' => $item['description_kr'],
                    ];

                    if ($section->id === 4) {
                        if (isset($item['video_en'])) {
                            $this->deleteFromHomeStorage($sectionItem->video_en);
                            $updateData['video_en'] = $item['video_en']->store('home', 'home_storage');
                        }

                        if (isset($item['video_ar'])) {
                            $this->deleteFromHomeStorage($sectionItem->video_ar);
                            $updateData['video_ar'] = $item['video_ar']->store('home', 'home_storage');
                        }

                        if (isset($item['video_kr'])) {
                            $this->deleteFromHomeStorage($sectionItem->video_kr);
                            $updateData['video_kr'] = $item['video_kr']->store('home', 'home_storage');
                        }
                    }

                    $sectionItem->update($updateData);

                    if (isset($item['image'])) {
                        $this->deleteFromHomeStorage($sectionItem->image);
                        $imagePath = $item['image']->store('home', 'home_storage');
                        $sectionItem->update(['image' => $imagePath]);
                    }
                } else {
                    $imagePath = $item['image']->store('home', 'home_storage');
                    $newItemData = [
                        'image' => $imagePath,
                        'cta_link' => $item['cta_link'],
                        'title_en' => $item['title_en'],
                        'title_ar' => $item['title_ar'],
                        'title_kr' => $item['title_kr'],
                        'description_en' => $item['description_en'],
                        'description_ar' => $item['description_ar'],
                        'description_kr' => $item['description_kr'],
                    ];

                    if ($section->id === 4) {
                        $newItemData['video_en'] = isset($item['video_en']) ? $item['video_en']->store('home', 'home_storage') : null;
                        $newItemData['video_ar'] = isset($item['video_ar']) ? $item['video_ar']->store('home', 'home_storage') : null;
                        $newItemData['video_kr'] = isset($item['video_kr']) ? $item['video_kr']->store('home', 'home_storage') : null;
                    }

                    $section->items()->create($newItemData);
                }
            }
        }

        $resource = new HomeSectionResource($section->load('items'));
        return response()->success(new SuccessResponse($resource, Response::HTTP_OK));
    }

    public function getSection($id)
    {
        $section = HomeSection::find($id);
        if (!$section) return response()->error(new ErrorResponse('section not found', Response::HTTP_NOT_FOUND));
        return response()->success(new SuccessResponse(new HomeSectionResource($section->load('items')), Response::HTTP_OK));
    }

    public function getBanner()
    {
        $section = HomeSection::where('type','=','slider')->first();
        if (!$section) return response()->error(new ErrorResponse('section not found', Response::HTTP_NOT_FOUND));
        return response()->success(new SuccessResponse(new HomeSectionResource($section->load('items')), Response::HTTP_OK));
    }

    public function destroy($id)
    {
        $section = HomeSection::find($id);
        if (!$section) return response()->error(new ErrorResponse('section not found', Response::HTTP_NOT_FOUND));

        foreach ($section->items as $item) {
            $this->deleteFromHomeStorage($item->image);
            $this->deleteFromHomeStorage($item->video_en);
            $this->deleteFromHomeStorage($item->video_ar);
            $this->deleteFromHomeStorage($item->video_kr);
            $item->delete();
        }

        $section->delete();
        return response()->success(new SuccessResponse("Section deleted successfully", Response::HTTP_OK));
    }

    public function getItem($id)
    {
        $item = HomeSectionItem::find($id);
        if (!$item) return response()->error(new ErrorResponse('item not found', Response::HTTP_NOT_FOUND));
        return response()->success(new SuccessResponse(new HomeSectionItemResource($item), Response::HTTP_OK));
    }

    public function createItem(Request $request)
    {
        $request->validate([
            'home_section_id' => 'required|integer',
            'image' => 'nullable|image',
            'cta_link' => 'nullable|url',
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'title_kr' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_kr' => 'nullable|string',
            'video_en' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'video_ar' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'video_kr' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
        ]);

        $homeSection = HomeSection::find($request->home_section_id);
        if (!$homeSection) return response()->error(new ErrorResponse('section not found', Response::HTTP_NOT_FOUND));

        $itemData = $request->only([
            'home_section_id', 'cta_link', 'title_en', 'title_ar', 'title_kr', 'description_en', 'description_ar', 'description_kr'
        ]);

        if ($request->has('image')) {
            $itemData['image'] = $request->image->store('home', 'home_storage');
        }

        if ($homeSection->id === 4) {
            if ($request->has('video_en')) {
                $itemData['video_en'] = $request->video_en->store('home', 'home_storage');
            }

            if ($request->has('video_ar')) {
                $itemData['video_ar'] = $request->video_ar->store('home', 'home_storage');
            }

            if ($request->has('video_kr')) {
                $itemData['video_kr'] = $request->video_kr->store('home', 'home_storage');
            }
        }

        $item = HomeSectionItem::create($itemData);
        return response()->success(new SuccessResponse(new HomeSectionItemResource($item), Response::HTTP_OK));
    }

    public function destroyItem($id)
    {
        $item = HomeSectionItem::find($id);
        if (!$item) return response()->error(new ErrorResponse('item not found', Response::HTTP_NOT_FOUND));

        $this->deleteFromHomeStorage($item->image);
        $this->deleteFromHomeStorage($item->video_en);
        $this->deleteFromHomeStorage($item->video_ar);
        $this->deleteFromHomeStorage($item->video_kr);
        $item->delete();

        return response()->success(new SuccessResponse("Item deleted successfully", Response::HTTP_OK));
    }

    public function updateItem(Request $request, $id)
    {
        $request->validate([
            'image' => 'nullable|image',
            'cta_link' => 'nullable|string',
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'title_kr' => 'required|string',
            'description_en' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'description_kr' => 'nullable|string',
            'video_en' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'video_ar' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
            'video_kr' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv|max:51200',
        ]);

        $item = HomeSectionItem::find($id);
        if (!$item) return response()->error(new ErrorResponse('item not found', Response::HTTP_NOT_FOUND));

        $updateData = $request->only([
            'cta_link', 'title_en','title_ar','title_kr','description_en','description_ar','description_kr'
        ]);

        if ($item->home_section_id === 4) {
            if ($request->has('video_en')) {
                $this->deleteFromHomeStorage($item->video_en);
                $updateData['video_en'] = $request->video_en->store('home', 'home_storage');
            }

            if ($request->has('video_ar')) {
                $this->deleteFromHomeStorage($item->video_ar);
                $updateData['video_ar'] = $request->video_ar->store('home', 'home_storage');
            }

            if ($request->has('video_kr')) {
                $this->deleteFromHomeStorage($item->video_kr);
                $updateData['video_kr'] = $request->video_kr->store('home', 'home_storage');
            }
        }

        $item->update($updateData);

        if ($request->has('image')) {
            $this->deleteFromHomeStorage($item->image);
            $imagePath = $request->image->store('home', 'home_storage');
            $item->update(['image' => $imagePath]);
        }

        return response()->success(new SuccessResponse(new HomeSectionItemResource($item), Response::HTTP_OK));
    }
     public function toggleActive(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:section,slide',
            'id' => 'required|integer',
        ]);
    
        $models = [
            'section' => \App\Models\HomeSection::class,
            'slide'   => \App\Models\Setting::class,
        ];
    
        $modelClass = $models[$request->type];
    
        $item = $modelClass::findOrFail($request->id);
    
        $item->active = !$item->active;
        $item->save();
        return response()->success(new SuccessResponse(new HomeSectionItemResource($item), Response::HTTP_OK));
    }


    private function deleteFromHomeStorage(?string $path): void
    {
        if (!empty($path)) {
            Storage::disk('home_storage')->delete($path);
        }
    }

}
