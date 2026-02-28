<?php
namespace App\Http\Resources;

class AccordionCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'accordions');
    }
}
