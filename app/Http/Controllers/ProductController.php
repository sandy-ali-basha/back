<?php

namespace App\Http\Controllers;

use App\Http\Data\AddAttributeData;
use App\Http\Data\AddProductData;
use App\Http\Data\AttributesFilterData;
use App\Http\Data\UpdateProductData;
use App\Http\Resources\ImagesCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductVariantsCollection;
use App\Http\Resources\ProductVariantsResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\ProductImage;
use App\Models\ProductModel;
use App\Services\ProductService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Region;
use Lunar\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Lunar\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{

    public ProductService $productService;
    public SettingService $settingService;

    public function __construct(ProductService $productService, SettingService $settingService)
    {
        $this->productService = $productService;
        $this->settingService = $settingService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $products        = $this->productService->getAllProducts();
        $productResource = new ProductCollection($products, true);
        $response        = new SuccessResponse($productResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
     
        try {
            DB::beginTransaction();
            
                    $data    = AddProductData::from($request);

                    $product = $this->productService->createProduct($data);
                    $this->productService->addQtyAndPrice($product->id,$data->options);
                    $product->withTranslation = true;
                    DB::commit();

                $productResource = new ProductResource($product);
                $response        = new SuccessResponse($productResource, Response::HTTP_OK);
                 DB::commit();
                return response()->success($response);


        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            Log::error($exception->getLine());
            Log::error($exception->getTraceAsString());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        $response = new SuccessResponse(["message"=>"products with regions created successfully","products_id"=>$productsIds], Response::HTTP_OK);

        return response()->success($response);
    }
        public function createVariant($id,Request $request){
        $product= $this->productService->addVariantToProduct($id,$request);
        $productServiceResource = new ProductResource($product);
        $response               = new SuccessResponse($productServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data    = UpdateProductData::from($request);
            $product = $this->productService->getById($id);
            if (!$product) {
                $response = new ErrorResponse('Product not found', Response::HTTP_NOT_FOUND);

                return response()->error($response);
            }
            $product = $this->productService->updateProduct($id, $data);
            $product->withTranslation = true;

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        $productServiceResource = new ProductResource($product);
        $response               = new SuccessResponse($productServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function updateProductOption(){

    }
    public function duplicateProduct($id)
    {
        try {
            DB::beginTransaction();
            $originalProduct = $this->productService->getById($id);

            if (!$originalProduct) {
                $response = new ErrorResponse('Product not found', Response::HTTP_NOT_FOUND);
                return response()->error($response);
            }
            $newProductData = $originalProduct->replicate(['translations']);

            $newProductData->save();
            $this->productService->addQtyAndPrice($newProductData->id, $originalProduct->prices()->first()->price->value, 0, $originalProduct->compare_price,$originalProduct->purchasable, $originalProduct->sku);
            $newProductData->withTranslation = true;
            $newProductData->ProductAttributesValues()->sync($originalProduct->ProductAttributesValues()->get());

            foreach ($originalProduct->images as $image) {
                $originalPath = $image->image_path;
                $type = $image->type;


                $imageName = $newProductData->id . '/' . basename($originalPath);
                if($type =='slider'){
                    $storagePath = 'slider';
                }
                else{
                    $storagePath = 'products_gallery';
                }
                $newImagePath = Storage::disk($storagePath)->copy($originalPath, $imageName);

                $newImage = $image->replicate();
                $newImage->image = $imageName;
                $newImage->product_id = $newProductData->id;
                $newImage->image_path = url("test/public/uploads/products/$newImagePath");
                $newImage->save();
            }


            DB::commit();
            
            $response = new SuccessResponse("product duplicated", Response::HTTP_OK);
            return response()->success($response);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->error($response);
        }
    }

    public function addImageToProduct($id, Request $request)
    {
        $product = $this->productService->getById($id);

        if (!$product) {
            $response = new ErrorResponse('Product not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = Storage::disk('products_gallery')->put($id, $image);

                ProductImage::create([
                    'product_id' => $product->id,
                    'type' => "gallery",
                    'image' => $imagePath,
                    'image_path' => url("test/public/uploads/products/$imagePath"),
                ]);
            }
        }


        $response = new SuccessResponse("product image uploaded", Response::HTTP_OK);

        return response()->success($response);
    }

    public function deleteImage($id)
    {

        $productImage = ProductImage::where('id', $id)->first();
       if (!$productImage){
           $response = new ErrorResponse('Image not found', Response::HTTP_NOT_FOUND);

           return response()->error($response);
       }
        Storage::disk('slider')->delete($productImage->image);
        $productImage->delete();
        $response = new SuccessResponse("image is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function show($id, Request $request)
    {
        $productService = $this->productService->getById($id);
    
        if (!$productService) {
            $response = new ErrorResponse('Product not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }


        $productService->withTranslation = $request->headers->has('translations');
        $productServiceResource          = new ProductResource($productService);
        $response                        = new SuccessResponse($productServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function updateProductVariant(Request $request,$id){
        $product = ProductVariant::find($id);
        $request->validate([
            'compare_price_start_date' => ['nullable', 'date'],
            'compare_price_end_date'   => ['nullable', 'date', 'after_or_equal:compare_price_start_date'],
            'options.compare_price_start_date' => ['nullable', 'date'],
            'options.compare_price_end_date'   => ['nullable', 'date', 'after_or_equal:options.compare_price_start_date'],
        ]);

        if (!$product) {
            $response = new ErrorResponse('Product variant not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        $payload = $request->input('options', $request->all());

        $this->productService->updateProuctVariant($product->id, $payload);
        $productVariantsResource          = new ProductVariantsResource($product->fresh());

        $response               = new SuccessResponse($productVariantsResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function deleteVariant(Request $request, $id)
    {
        $variant = $this->productService->getVariantById($id);

        if (!$variant) {
            $response = new ErrorResponse('Variant not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }

        // Count the number of variants for the product
        $variantCount = ProductVariant::where('product_id', $variant->product_id)->count();

        // If it's the last variant, prevent deletion
        if ($variantCount <= 1) {
            $response = new ErrorResponse('Cannot delete the last variant of a product', Response::HTTP_BAD_REQUEST);
            return response()->error($response);
        }

        // Proceed with deletion
        $variant->delete();

        $response = new SuccessResponse("Variant deleted successfully", Response::HTTP_OK);
        return response()->success($response);
    }
    public function getProductVariants(Request $request,$id){
        $variants = $this->productService->getVariants($id);
        $productVariantsResource          = new ProductVariantsCollection($variants);

        $response               = new SuccessResponse($productVariantsResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addAttribute(Request $request, $id)
    {
        $data      = AddAttributeData::from($request);
        $product   = $this->productService->getById($id);
        $attribute = $this->productService->getAttributeById($data->attribute_id);

        if (!$product || !$attribute) {
            $response = new ErrorResponse('Product/attribute not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $product                  = $this->productService->addAttributes($id, $data);
        $product->withTranslation = true;
        $productServiceResource   = new ProductResource($product);
        $response                 = new SuccessResponse($productServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }
    public function attributeFilter(Request $request)
    {
        $data            = AttributesFilterData::from($request);
        $products        = $this->productService->filterData($data->filters, $data->brand_id, $data->product_type_id, $data->min_price, $data->max_price);
        $productResource = new ProductCollection($products);
        $response        = new SuccessResponse($productResource, Response::HTTP_OK);

        return response()->success($response);

    }
    public function similarProducts(int $id)
    {
        $products        = $this->productService->getSimilarProducts($id);
        $productResource = new ProductCollection($products);
        $response        = new SuccessResponse($productResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function destroy($id)
    {

        Storage::disk('products_gallery')->deleteDirectory($id);
        Storage::disk('slider')->deleteDirectory($id);

        $this->productService->deleteProduct($id);


        $response = new SuccessResponse("product is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function uploadImagesSlider(Request $request, $id)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $image) {
                $imagePath = Storage::disk('slider')->put($id, $image);

                ProductImage::create([
                    'product_id' => $product->id,
                    'type' => "slider",
                    'image' => $imagePath,
                    'image_path' => url("test/public/uploads/slider/$imagePath"),
                ]);
            }
        }
        $response = new SuccessResponse('Images uploaded successfully', Response::HTTP_OK);

        return response()->success($response);
    }



    public function getImagesSlider($id)
    {
        $product = ProductModel::findOrFail($id);
        $images  = $product->slider()->get();

        $productServiceResource   = new ImagesCollection($images);
        $response                 = new SuccessResponse($productServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }

   public function searchProducts(Request $request) {
    $search = $request->query('name', '');
    $locale = App::getLocale();

    $product = ProductModel::whereRaw(
        "LOWER(attribute_data) COLLATE utf8mb4_general_ci LIKE ?",
        ['%' . strtolower($search) . '%']
    )
    ->where('status', 'active')
    ->get();

    $collection = new ProductCollection($product);
    $response = new SuccessResponse($collection, Response::HTTP_OK);

    return response()->success($response);
}


    public function getOffers() {
        $products        = $this->productService->getProudctsWithOffers();
        $productResource = new ProductCollection($products);
        $response        = new SuccessResponse($productResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function toggleProduct(Request $request, $id) {
        $product = $this->productService->getById($id);
        if (!$product) {
            $response = new ErrorResponse('Product not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        if ($product->status === 'active') {
            DB::table('lunar_products')->where('id', $id)->update(['status' => 'inactive']);
        } else {
            DB::table('lunar_products')->where('id', $id)->update(['status' => 'active']);
        }
        $product->refresh();

        $productResource = new ProductResource($product);
        $response        = new SuccessResponse($productResource, Response::HTTP_OK);

        return response()->success($response);
    }

        public function bulkDelete(Request $request)
    {

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:lunar_products,id',
        ]);

        $productIds = $request->input('product_ids');
        try {
            DB::beginTransaction();

            $productImages = ProductImage::whereIn('product_id', $productIds)->get();
            foreach ($productImages as $image) {
                Storage::disk('products_gallery')->delete($image->image);
                $image->delete();
            }

            ProductModel::whereIn('id', $productIds)->delete();

            DB::commit();
            $response = new SuccessResponse('Products deleted successfully', Response::HTTP_OK);
            return response()->success($response);

        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
            return response()->error($response);
        }
    }

}
