<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCollection extends JsonResource
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
        parent::__construct($resource, 'users');
    }

}
