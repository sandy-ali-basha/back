<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandSlideResource extends JsonResource
{
    public function toArray($request)
    {

        $data = [
            'id' => $this->id,
            'image' => $this->image,
            'image_path' => url($this->image_path),
            'brand_id' => $this->brandPage->brand_id,
        ];

        $data['translations'] = $this->translations;


        return $data;

    }
}
