<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributesResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'tag'=>$this->tag,
            'status'=>$this->status,
            'navActive' => (bool) $this->nav_active,
            'image'=> $this->image_url,
            'translations' => $this->translations
        ];

        return $data;

    }
}
