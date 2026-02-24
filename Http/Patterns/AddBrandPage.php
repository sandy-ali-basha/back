<?php

namespace App\Http\Patterns;

use App\Models\BrandPages;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class AddBrandPage implements IOperations
{
    public  function doOperation(array $data)
    {
        $brandPage = BrandPages::create($data);

        $brandPageTranslation = $brandPage->translations()->get();
        $brandPage->translations = $brandPageTranslation;

        return $brandPage;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Brand page');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD Brand page IN brand CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
