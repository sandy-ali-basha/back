<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->image_url,
            'cta_link' => $this->cta_link,
            'title_en' => $this->title_en,
            'description_en' => $this->description_en,
            'title_ar' => $this->title_ar,
            'description_ar' => $this->description_ar,
            'title_kr' => $this->title_ar,
            'description_kr' => $this->description_ar,
        ];
    }
}
