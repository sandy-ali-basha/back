<?php


namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Partner;


class ContactResource extends JsonResource
{
    public function toArray($request)
    {
        $data   = [
          'id'=> $this->id ?? "",
          "companyName"=>$this->companyName,
          'facebook'=> $this->facebook ?? '',
          'instagram' => $this->instagram ?? "",
          'linkedin' => $this->linkedin ?? "",
          'whatsapp' => $this->whatsapp ?? "",
          'email' =>  $this->email ?? "",
          'locations'=>$this->locations
        ];
        
        return $data;

    }
}
