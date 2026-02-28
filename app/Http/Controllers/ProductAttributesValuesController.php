<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductAttributesValuesService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\ProductAttributesValuesCollection;
use App\Http\Resources\ProductAttributesValuesResource;
use App\Http\Data\AddProductAttributesValuesData;
use App\Http\Data\UpdateProductAttributesValuesData;
class ProductAttributesValuesController extends Controller
{

   public ProductAttributesValuesService $productAttributesValuesService;

    public function __construct(ProductAttributesValuesService $productAttributesValuesService)
    {
        $this->productAttributesValuesService = $productAttributesValuesService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $attId)
    {
        $productAttributesValues = $this->productAttributesValuesService->getAllProductAttributesValues($attId, request()->query('all'));
        $productAttributesValuesResource = new ProductAttributesValuesCollection($productAttributesValues);
        $response = new SuccessResponse($productAttributesValuesResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexWebsite(int $attId)
    {
        $productAttributesValues = $this->productAttributesValuesService->getAllProductAttributesValuesWebsite($attId);
        $productAttributesValuesResource = new ProductAttributesValuesCollection($productAttributesValues);
        $response = new SuccessResponse($productAttributesValuesResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddProductAttributesValuesData::from($request);
        $productAttributesValues = $this->productAttributesValuesService->createProductAttributesValues($data);
        $productAttributesValues->withTranslation= true;
        $productAttributesValuesResource = new ProductAttributesValuesResource($productAttributesValues);
        $response = new SuccessResponse($productAttributesValuesResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function show($id,Request $request)
    {
        $productAttributesValues = $this->productAttributesValuesService->getById($id);
        if (!$productAttributesValues) {
            $response = new ErrorResponse('product attributes values not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $productAttributesValues->withTranslation = $request->headers->has('translations');
        $productAttributesValuesResource = new ProductAttributesValuesResource($productAttributesValues);
        $response = new SuccessResponse($productAttributesValuesResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $productAttributesValues = $this->productAttributesValuesService->getById($id);
        if (!$productAttributesValues) {
            $response = new ErrorResponse('product attributes values not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $data = UpdateProductAttributesValuesData::from($request);
        $productAttributesValues = $this->productAttributesValuesService->updateProductAttributesValues($id, $data);
        $productAttributesValues->withTranslation= true;
        $productAttributesValuesResource = new ProductAttributesValuesResource($productAttributesValues);
        $response = new SuccessResponse($productAttributesValuesResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $productAttributesValues = $this->productAttributesValuesService->getById($id);
        if (!$productAttributesValues) {
            $response = new ErrorResponse('product attributes values not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $this->productAttributesValuesService->deleteProductAttributesValues($id);
        $response = new SuccessResponse("product attributes values is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
