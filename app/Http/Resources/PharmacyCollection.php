<?php
namespace App\Http\Resources;

class PharmacyCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'pharmacies');
    }
}

