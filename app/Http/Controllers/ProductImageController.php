<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProductImageController extends Controller
{
    public function index($productId,$type)
    {
        $images = ProductImage::where(['product_id'=> $productId,'type'=>$type])->get();

        if ($images->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No images found for this product.',
                'code' => Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $images,
            'code' =>Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    // Add images to a product
    public function store($productId, Request $request,$type)
    {
        if (!$request->hasFile('images')) {
            return response()->json([
                'success' => false,
                'message' => 'No images provided.',
                'code' =>Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            if($type =='slider'){
                $storagePath = 'slider';
                  $type = 'slider';
            }
            elseif($type =='products_features'){
                                $storagePath = 'products_features';
                                 $type = 'products_features';

            }
            else{
                $storagePath = 'products_gallery';
                $type = 'products';
            }
            $imagePath = Storage::disk($storagePath)->put($productId, $image);

            $productImage = ProductImage::create([
                'product_id' => $productId,
                'type' => $type,
                'image' => $imagePath,
                'image_path' => url("test/public/uploads/$type/$imagePath"),
            ]);

            $uploadedImages[] = $productImage;
        }

        return response()->json([
            'success' => true,
            'data' => $uploadedImages,
            'code' =>Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    // Delete an image
    public function destroy($productId, $imageId,$type)
    {
        $image = ProductImage::where('product_id', $productId)->where('id', $imageId)->first();

        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.',
                'code' =>Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }
          if($type =='slider'){
                $storagePath = 'slider';
                  $type = 'slider';
            }
            elseif($type =='products_features'){
                                $storagePath = 'products_features';
                                 $type = 'products_features';

            }
            else{
                $storagePath = 'products_gallery';
                $type = 'products';
            }
        Storage::disk($storagePath)->delete($image->image);

        // Delete the image record from the database
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
            'code' =>Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    // Replace an image
    public function update($productId, $imageId, Request $request,$type)
    {
        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'No image provided for replacement.',
                'code' =>Response::HTTP_BAD_REQUEST
            ], Response::HTTP_BAD_REQUEST);
        }

        $image = ProductImage::where('product_id', $productId)->where('id', $imageId)->first();
           if($type =='slider'){
                $storagePath = 'slider';
                  $type = 'slider';
            }
            elseif($type =='products_features'){
                                $storagePath = 'products_features';
                                 $type = 'products_features';

            }
            else{
                $storagePath = 'products_gallery';
                $type = 'products';
            }
        if (!$image) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.',
                'code' =>Response::HTTP_NOT_FOUND
            ], Response::HTTP_NOT_FOUND);
        }

        // Delete the old image file from storage
        Storage::disk($storagePath)->delete($image->image);

        // Upload the new image
        $newImagePath = Storage::disk($storagePath)->put($productId, $request->file('image'));

        // Update the image record
        $image->update([
            'image' => $newImagePath,
            'image_path' => url("test/public/uploads/$type/$newImagePath"),
        ]);

        return response()->json([
            'success' => true,
            'data' => $image,
            'message' => 'Image replaced successfully.',
            'code' =>Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
