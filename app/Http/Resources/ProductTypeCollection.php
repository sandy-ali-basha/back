<?php
namespace App\Http\Resources;

class ProductTypeCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'producttypes');
    }
}
