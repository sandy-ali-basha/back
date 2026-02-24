<?php
namespace App\Http\Resources;

class ContactCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'contact_info');
    }
}
