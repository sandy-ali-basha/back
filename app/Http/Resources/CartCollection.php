<?php
namespace App\Http\Resources;

class CartCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'carts');
    }
}
