<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeSectionResource extends JsonResource
{
    public function toArray($request)
    {
    
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title_en' => $this->title,
            'description_en' => $this->description,
            'title_ar' => $this->title_ar,
            'description_ar' => $this->description_ar,
                'title_kr' => $this->title_ar,
            'description_kr' => $this->description_ar,
            'items' => HomeSectionItemResource::collection($this->items),
        ];
    }
}
