<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $addData=false;
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param bool $addData
     * @return void
     */
    public function __construct($resource, bool $addData=false)
    {
        $this->resource = $resource;
        $this->addData = $addData;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
            'token'=>$this->token,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
        if ($this->roles) {
            $data['roles'] = $this->roles;
        }
        if ($this->addData) {
            $data = array_merge($data, [
                'provider_id' => $this->provider_id,
                'provider_name' => $this->provider_name,
                'provider_token' => $this->provider_token,
            ]);
        }
        return $data;
    }
}
