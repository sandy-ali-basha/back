<?php
namespace App\Http\Resources;

class BrandPageCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'brand_pages');
    }
}
