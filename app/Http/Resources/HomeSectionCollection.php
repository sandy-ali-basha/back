<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeSectionCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'home_sections');
    }
}
