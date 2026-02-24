<?php
namespace App\Http\Patterns;

use App\Models\AboutUs;
use App\Models\Logs;
use App\Models\AboutUsModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateAboutUs implements IOperations
{
    public  function doOperation(array $data)
    {
        $cleanData = array_filter($data["data"], fn ($value) => !is_null($value));

        $aboutus = AboutUs::find($data['id']);
        $aboutus->update($cleanData);
         if ($data["data"]["image_url"]) {
            $aboutus
                ->clearMediaCollection('about_us')
                ->addMedia($data["data"]["image_url"])
                ->toMediaCollection('about_us', 'settings_files');
        }
    
        $aboutusTranslation = $aboutus->translations()->get();
        $aboutus->translations = $aboutusTranslation;

        return $aboutus;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'AboutUs');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update AboutUs IN AboutUs CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
