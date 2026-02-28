<?php
namespace App\Http\Resources;

class PartnerCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'partner');
    }
}
