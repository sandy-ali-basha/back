<?php
namespace App\Http\Resources;

class SettingCollection extends MainCollection
{
    public function __construct($resource)
    {
        parent::__construct($resource, 'settings');
    }

    public function toArray($request)
    {    
        return $this->collection->mapWithKeys(function($setting, $key) {
            return [$setting->name => $setting];
        });
    }
}
