<?php

namespace App\Http\Resources;

class BrandSlideCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'brand_slides');
    }
}
