<?php
namespace App\Http\Resources;

class OrderCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'orders');
    }
}
