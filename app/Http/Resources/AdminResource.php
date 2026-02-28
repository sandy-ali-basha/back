<?php

namespace App\Http\Resources;

use Database\Seeders\permissions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    protected $addData=false;
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param bool $addData
     * @return void
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
       
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

            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
        if ($this->roles) {
            $data['roles'] = $this->roles;
            foreach ($data['roles'] as $key => $value) {
                if (!isset($data['roles'][$key]['permissions'])) {
                    $data['roles'][$key]['permissions'] = $this->roles[$key]->permissions;
                }
            }
        }
    
        return $data;
    }
}
