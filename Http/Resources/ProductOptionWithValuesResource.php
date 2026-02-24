<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProductOptionWithValuesResource extends JsonResource
{
    public function toArray($request)
    {

        $data = [
            "id"=>$this->id,
            "name"=>$this->translate('name',App::getLocale()),
            "values"=>collect(new ProductOptionValuesCollection($this->values)),
        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
