<?php
namespace App\Http\Resources;

class BrandProductCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'products');
    }
}
