<?php
namespace App\Http\Patterns;;

use App\Models\AboutUs;
use App\Models\Logs;
use App\Models\AboutUsModel;
use Illuminate\Support\Facades\Log;

class DeleteAboutUs implements IOperations
{
    public function doOperation(array $data)
    {
        $aboutus = AboutUs::find($data['id']);
        $aboutus->deleteTranslations();
        return $aboutus->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'AboutUs');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW AboutUs us IN AboutUs CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
