<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        $brand = $this->translate(App::getLocale()??"en");
        $data   = [
            'id' => $this->id,
            'name' => $brand ? $brand->name : '',
            'num_of_products' => $this->bproducts->count(), 
            'havePage'=> $this->pages()->count() > 0,
        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }
        foreach ($this->getMedia('image') as $image){
            $data['images'][] = $image->getUrl() ;
        }

        return $data;

    }
}
