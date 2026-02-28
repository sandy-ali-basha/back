<?php

namespace App\Http\Controllers;

use App\Http\Data\AddProductOptionData;
use App\Http\Data\AddProductOptionValuesData;
use App\Http\Data\UpdateProductOptionData;
use App\Http\Resources\ProductOptionCollection;
use App\Http\Resources\ProductOptionWithValuesCollection;
use App\Http\Resources\ProductOptionResource;
use App\Http\Resources\ProductOptionValuesCollection;
use App\Http\Resources\ProductOptionValuesResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\ProductOptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductOptionController extends Controller
{

    public ProductOptionService $productOptionService;
    public function __construct(ProductOptionService $productOptionService)
    {
        $this->productOptionService = $productOptionService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request)
    {
        $productOptions         = $this->productOptionService->getAllProductOptions();
        $productOptions->withValues = $request->headers->has('values');

        $productOptionsResource = new ProductOptionCollection($productOptions);
        if ($productOptions->withValues){
            $productOptionsResource = new ProductOptionWithValuesCollection($productOptions);
        }
        $response               = new SuccessResponse($productOptionsResource, Response::HTTP_OK);

        return response()->success($response);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data                            = AddProductOptionData::from($request);
        $productOptions                  = $this->productOptionService->createProductOption($data);
        $productOptions->withTranslation = true;
        $productOptions->withValues = false;
        $productOptionsResource          = new ProductOptionResource($productOptions);
        $response                        = new SuccessResponse($productOptionsResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addValues(Request $request, $id)
    {
        $data                            = AddProductOptionValuesData::from($request);
        $productOptions                  = $this->productOptionService->createProductOptionValues($data, $id);
        $productOptions->withTranslation = true;
        $productOptions->withValues = false;
        $productOptionsResource          = new ProductOptionValuesResource($productOptions);
        $response                        = new SuccessResponse($productOptionsResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function getValues($id)
    {
        $productOptions                  = $this->productOptionService->getProductOptionValues($id);
        $productOptions->withTranslation = true;
        $productOptions->withValues = false;
        $productOptionsResource          = new ProductOptionValuesCollection($productOptions);
        $response                        = new SuccessResponse($productOptionsResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function show($id, Request $request)
    {
        $productOption = $this->productOptionService->getById($id);
        if (!$productOption) {
            $response = new ErrorResponse('Product Option not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $productOption->withValues = false;
        $productOption->withTranslation = $request->headers->has('translations');
        $productOptionsSResource        = new ProductOptionResource($productOption);
        $response                       = new SuccessResponse($productOptionsSResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data                           = UpdateProductOptionData::from($request);
        $productOption                  = $this->productOptionService->updateProductOption($id, $data);
        $productOption->withTranslation = true;
        $productOption->withValues = false;
        $productOptionResource          = new ProductOptionResource($productOption);
        $response                       = new SuccessResponse($productOptionResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function destroy($id)
    {
        $this->productOptionService->deleteProductOption($id);
        $response = new SuccessResponse("product option is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function destroyValue($id)
    {
        $this->productOptionService->deleteProductOptionValue($id);
        $response = new SuccessResponse("product option is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
