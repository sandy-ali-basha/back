<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductTypeService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\ProductTypeCollection;
use App\Http\Resources\ProductTypeResource;
use App\Http\Data\AddProductTypeData;
use App\Http\Data\UpdateProductTypeData;
class ProductTypeController extends Controller
{

   public ProductTypeService $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productTypes = $this->productTypeService->getAllproductTypes();
        $productTypeResource = new productTypeCollection($productTypes);
        $response = new SuccessResponse($productTypeResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddproductTypeData::from($request);
        $productType = $this->productTypeService->createproductType($data);
        $productType->withTranslation= true;
        $productTypeResource = new productTypeResource($productType);
        $response = new SuccessResponse($productTypeResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function show($id,Request $request)
    {
        $productTypeService = $this->productTypeService->getById($id);
        if (!$productTypeService) {
            $response = new ErrorResponse('Product Type not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $productTypeService->withTranslation = $request->headers->has('translations');
        $productTypeServiceResource = new productTypeResource($productTypeService);
        $response = new SuccessResponse($productTypeServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data = UpdateProductTypeData::from($request);
        $productType = $this->productTypeService->updateproductType($id, $data);
        $productType->withTranslation= true;
        $productTypeServiceResource = new productTypeResource($productType);
        $response = new SuccessResponse($productTypeServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $type = $this->productTypeService->getById($id);
        if (!$type){
            $response = new ErrorResponse('Type not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if ($type->products()->count()!=0){
            $response = new ErrorResponse("Can't delete type with related products", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $this->productTypeService->deleteProductType($id);
        $response = new SuccessResponse("product Type is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
