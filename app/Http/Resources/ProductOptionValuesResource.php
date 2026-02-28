<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Lunar\Models\Product;
use Lunar\Models\ProductOptionValue;

class ProductOptionValuesResource  extends JsonResource
{
    public function toArray($request)
    {
        $product = ProductOptionValue::find($this->id);
        $data = [
            "id"=>$this->id,
            "option_id"=>$this->option->id,
            "name"=>$product->translate('name',App::getLocale()),
        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
