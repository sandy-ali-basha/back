<?php
namespace App\Http\Resources;

class BrandCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'brands');
    }
}
