<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class HomeSlideResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = App::getLocale() ?? 'en';
        $locales = ['ar', 'en', 'kr'];
        $data   = [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->value[$locale]['title'] ?? '',
            'link' => $this->value['link'] ?? '',
            'text' => $this->value[$locale]['text'] ?? '',
        ];
        foreach ($this->getMedia('image') as $key => $value) {
            $data['image'] = $value->getUrl();
        }
        if (request()->header('translations')) {
            $data['translations'] = [];
            foreach ($locales as $key => $value) {
                $data['translations'][] = [
                    'locale' => $value,
                    'title' => $this->value[$value]['title'],
                    'text' => $this->value[$value]['text'],
                ];
            }
        }
        return $data;

    }
}
