<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Partner;


class LocationResource extends JsonResource
{
    public function toArray($request)
    {
        $data   = [
          'id'=> $this->id ?? "",
          'map_url'=> $this->map_url ?? '',
          'office_name' => $this->office_name ?? "",
          'address' => $this->address ?? "",
          'is_main' => $this->is_main ?? ""
        ];
         if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }
        return $data;

    }
}
