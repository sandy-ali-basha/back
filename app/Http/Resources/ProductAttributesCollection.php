<?php
namespace App\Http\Resources;

class ProductAttributesCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'product_attributes');
    }
}
