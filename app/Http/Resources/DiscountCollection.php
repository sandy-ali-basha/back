<?php
namespace App\Http\Resources;

class DiscountCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'dicounts');
    }
}
