<?php

namespace App\Http\Controllers;

use App\Services\productAttributesService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\ProductAttributesCollection;
use App\Http\Resources\ProductAttributesResource;
use App\Http\Data\AddProductAttributesData;
use App\Http\Data\UpdateProductAttributesData;
use App\Models\ProductAttributes;
class ProductAttributesController extends Controller
{

   public productAttributesService $productAttributesService;

    public function __construct(productAttributesService $productAttributesService)
    {
        $this->productAttributesService = $productAttributesService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productAttributes = $this->productAttributesService->getAllProductAttributes(request()->query('all'));
        $productAttributesResource = new ProductAttributesCollection($productAttributes);
        $response = new SuccessResponse($productAttributesResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddProductAttributesData::from($request);
        $product_attributes = $this->productAttributesService->createProductAttributes($data);
        $product_attributes->withTranslation= true;
        $product_attributesResource = new ProductAttributesResource($product_attributes);
        $response = new SuccessResponse($product_attributesResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function show($id,Request $request)
    {
        $product_attributesService = $this->productAttributesService->getById($id);
        if (!$product_attributesService) {
            $response = new ErrorResponse('product  attributes not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $product_attributesService->withTranslation = $request->headers->has('translations');
        $product_attributesServiceResource = new ProductAttributesResource($product_attributesService);
        $response = new SuccessResponse($product_attributesServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $product_attributesService = $this->productAttributesService->getById($id);
        if (!$product_attributesService) {
            $response = new ErrorResponse('product  attributes not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $data = UpdateProductAttributesData::from($request);
        $product_attributesService = $this->productAttributesService->updateProductAttributes($id, $data);
        $product_attributesService->withTranslation= true;
        $product_attributesServiceResource = new ProductAttributesResource($product_attributesService);
        $response = new SuccessResponse($product_attributesServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $product_attributesService = $this->productAttributesService->getById($id);
        if (!$product_attributesService) {
            $response = new ErrorResponse('product attributes not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $this->productAttributesService->deleteProductAttributes($id);
        $response = new SuccessResponse("product_attributes is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
    
    public function toggleStatus($id)
    {
        $category = ProductAttributes::find($id);

        if (!$category) {
            return response()->success(new SuccessResponse("Category is not found", 404, 'ProductAttributes not found'));
        }

        // Toggle as boolean true/false
        $category->status = !((bool) $category->status);
        $category->save();

        return response()->success(new SuccessResponse([
            'id' => $category->id,
            'status' => (bool) $category->status,
        ], 200, 'updated successfully'));
    }

}
