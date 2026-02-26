<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
        if (($this->options['type'] ?? '') === 'video') {
            $data['videos'] = $this->video;
        }

        return $data;

    }
}
