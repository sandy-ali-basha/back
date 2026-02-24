<?php
namespace App\Http\Resources;

class ProductOptionValuesCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'product_options_values');
    }
}
