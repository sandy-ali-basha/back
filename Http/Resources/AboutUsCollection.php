<?php
namespace App\Http\Resources;

class AboutUsCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'aboutuss');
    }
}
