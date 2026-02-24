<?php

namespace App\Http\Patterns;

use App\Models\Logs;
use App\Models\AboutUs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;


class AddAboutUs implements IOperations
{
    public function doOperation(array $data)
    {
        $aboutus = AboutUs::create($data);
         if (isset($data['image_url'])) {
            $aboutus
                ->addMedia($data["image_url"])
                ->toMediaCollection('about_us', 'settings_files');
         }
        $aboutusTranslation = $aboutus->translations()->get();
        $aboutus->translations = $aboutusTranslation;

        return $aboutus;
    
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'AboutUs');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD AboutUs IN AboutUs CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
