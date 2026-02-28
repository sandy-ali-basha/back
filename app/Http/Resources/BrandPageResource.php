<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BrandPageResource extends JsonResource
{
    public function toArray($request)
    {
        $brandPage = $this->translate(App::getLocale()??"en");
        $data   = [
            'id' => $this->id,
            'name' => $brandPage->name,
            'text' => $brandPage->text,
            'brand_id' => $this->brand_id,


        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }
        if ($this->products) {
            $data['products'] = collect(new BrandProductCollection($this->products));
        }
        if($this->brand->getMedia('image')){
            $data['image'] = $this->brand->getMedia('image')->first()->getUrl() ;
        }
        else{
          $data['image'] = '';  
        }
        $data['slides'] = collect(new BrandSlideCollection($this->slides));

        return $data;

    }
}
