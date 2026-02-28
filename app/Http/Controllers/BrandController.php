<?php

namespace App\Http\Controllers;

use App\Http\Data\AddBrandData;
use App\Http\Data\AddBrandPageData;
use App\Http\Data\AddBrandPageSlideData;
use App\Http\Data\UpdateBrandData;
use App\Http\Data\UpdateBrandPageData;
use App\Http\Resources\BrandCollection;
use App\Http\Resources\BrandPageResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\BrandSlideCollection;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\BrandPages;
use App\Models\BrandSlide;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class BrandController extends Controller
{
    public BrandService $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        $brands        = $this->brandService->getAllBrands();
        $brandResource = new BrandCollection($brands);
        $response      = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    // Store a newly created brand
    public function store(Request $request)
    {
        $data                   = AddBrandData::from($request);
        $brand                  = $this->brandService->createBrand($data);
        $brand->withTranslation = true;
        $brandResource          = new BrandResource($brand);
        $response               = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addImageToBrand($id, Request $request)
    {
        $brand = $this->brandService->getById($id);
        if (!$brand) {
            $response = new ErrorResponse('brand not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $brand->updateMedia([], 'image');

        $brand->addMedia($request->image)->toMediaCollection('image');
        $response = new SuccessResponse("brand image uploaded", Response::HTTP_OK);

        return response()->success($response);
    }

    // Display the specified brand
    public function show($id, Request $request)
    {
        $brand = $this->brandService->getById($id);
        if (!$brand) {
            $response = new ErrorResponse('Brand not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $brand->withTranslation = $request->headers->has('translations');
        $brandResource          = new BrandResource($brand);
        $response               = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function destroy($id)
    {
        $brand = $this->brandService->getBrandId($id);
        if (!$brand) {
            $response = new ErrorResponse('brand not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if ($brand->products()->count() != 0) {
            $response = new ErrorResponse("Can't delete brand with related products", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $this->brandService->deleteBrand($id);
        $response = new SuccessResponse("brand is deleted", Response::HTTP_OK);

        return response()->success($response);
    }


    public function createBrandPages(Request $request)
    {
        $data                   = AddBrandPageData::from($request);
        if (BrandPages::where('brand_id',$data->brand_id)->first()) {
            $response = new ErrorResponse("Can't add brand page twice", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        $brandPage = $this->brandService->addBrandPage($data);
        $brandPage->withTranslation = $request->headers->has('translations');
        $brandProduct = $this->brandService->getProduct($data->brand_id);
        $brandPage->products = $brandProduct;
        $brandResource          = new BrandPageResource($brandPage);
        $response = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function updateBrandPage(int $id, Request $request)
    {
        $data                   = UpdateBrandPageData::from($request);

        $brandPage = BrandPages::where('brand_id', $data->brand_id)->first();
         $brandPage->update($data->toArray());

        $brandProduct = $this->brandService->getProduct($data->brand_id);
        $brandPage->products = $brandProduct;
        $brandPage->withTranslation = $request->headers->has('translations');
        $brandResource          = new BrandPageResource($brandPage);
        $response = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function getBrandPage($id,Request $request){
        $brandPage = BrandPages::where('brand_id',$id)->first();
        $brandProduct = $this->brandService->getProduct($id);
        $brandPage->products = $brandProduct;
        $brandPage->withTranslation = $request->headers->has('translations');
        $brandResource          = new BrandPageResource($brandPage);
        $response = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function update(Request $request, $id)
    {
        $data                   = UpdateBrandData::from($request);
        $brand                  = $this->brandService->updateBrand($id, $data);
        $brand->withTranslation = true;
        $brandResource          = new BrandResource($brand);
        $response               = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addPageSlides( Request $request)
    {

        $data                       = AddBrandPageSlideData::from($request);
        $brandPage                  = BrandPages::where('brand_id', $data->brand_id)->first();
        $brandPage->withTranslation = $request->headers->has('translations');


        foreach ($data->slides as $slideData) {

            $imagePath = Storage::disk('brand_slider')->put($data->brand_id, $slideData['image']);
            $slideData = array_merge($slideData, [
                'image_path' => "test/public/uploads/brand_slider/$imagePath",
                'image' => $imagePath,
                'brand_page_id' => $brandPage->id,
            ]);
           BrandSlide::create($slideData);
        }
        $slides             = BrandSlide::where('brand_page_id', $brandPage->id)->with('brandPage')->get();
        $brandSlideResource = new BrandSlideCollection($slides);
        $response           = new SuccessResponse($brandSlideResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function deleteBrandPage(int $id)
    {
        Storage::disk('brand_slider')->deleteDirectory($id);
        $brandPage = BrandPages::where('brand_id',$id)->first();
        $brandPage->delete();
        $response = new SuccessResponse("brand  page is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function deleteSlide(int $id)
    {
        $brandSlide = BrandSlide::where('id', $id)->first();
        if (!$brandSlide) {
            $response = new ErrorResponse('Slide not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        Storage::disk('brand_slider')->delete($brandSlide->image);
        $brandSlide->delete();
        $response = new SuccessResponse("slide is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
}
