<?php
namespace App\Http\Resources;

class ProductVariantsCollection extends MainCollection
{
    protected $all = false;
    public function __construct($resource, $all=false)
    {
        $this->all = $all;
        parent::__construct($resource, 'variants');
    }
}
