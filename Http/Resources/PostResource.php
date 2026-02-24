<?php


namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'text' => $this->text,
            'title' => $this->title,
            'image' => $this->image,
            'date' => $this->created_at,
            "created_at" => (new Carbon($this->created_at))->format("Y-m-d h:m A"),
            "updated_at" => (new Carbon($this->updated_at))->format("Y-m-d h:m A"),
               'translations' => $this->translations,

        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
