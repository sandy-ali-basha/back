<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class SettingResource extends JsonResource
{
    public function toArray($request)
    {
        $data   = [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'options' => $this->options,
        ];
        foreach ($this->getMedia('image') as $key => $value) {
            $data['image'] = $value->getUrl();
        }
        foreach ($this->getMedia('video') as $key => $value) {
            $data['video'] = $value->getUrl();
        }
        
        
        return $data;

    }
}
