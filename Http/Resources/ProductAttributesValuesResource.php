<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributesValuesResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'value' => $this->value,
            'tag'=>$this->tag,
            'translations' => $this->translations,

        ];

        return $data;

    }
}
