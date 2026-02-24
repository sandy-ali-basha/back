<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ProductOptionResource extends JsonResource
{
    public function toArray($request)
    {

        $data = [
            "id"=>$this->id,
            "name"=>$this->translate('name',App::getLocale()),
        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
