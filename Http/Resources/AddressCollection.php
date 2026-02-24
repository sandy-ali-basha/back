<?php
namespace App\Http\Resources;

class AddressCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'addresses');
    }
}
