<?php
namespace App\Http\Resources;

class ProductOptionWithValuesCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'product_options');
    }
}
