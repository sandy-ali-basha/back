<?php

namespace App\Http\Patterns;

use App\Models\ProductAttributes;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class AddProductAttributes implements IOperations
{
    public  function doOperation(array $data)
    { 
        $image = $data['image_url'] ?? null;
        if (array_key_exists('navActive', $data)) {
            $data['nav_active'] = $data['navActive'];
            unset($data['navActive']);
        }
        unset($data['image_url']);
        $product_attributes = ProductAttributes::create($data);
          if ($image) {
                $product_attributes
                    ->addMedia($image)
                    ->toMediaCollection('product_attributes');
            }
        $product_attributesTranslation = $product_attributes->translations()->get();
        $product_attributes->translations = $product_attributesTranslation;

        return $product_attributes;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'product_attributes');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD product_attributes IN product_attributes CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
