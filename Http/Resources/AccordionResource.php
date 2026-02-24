<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AccordionResource extends JsonResource
{
    public function toArray($request)
    {
        $acc = $this->translate(App::getLocale()??"en");
        $data = [
            'id' => $this->id,
            'description' => $acc->description,
            'title' => $acc->title,
            'product_id' => $this->product_id,

        ];
            $data['translations'] = $this->translations;

        return $data;

    }
}
