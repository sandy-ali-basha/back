<?php

namespace App\Http\Patterns;

use App\Models\Career;
use App\Models\Logs;
use Illuminate\Support\Facades\App;

class AddCareer implements IOperations
{
    public  function doOperation(array $data)
    {

        $career = Career::create($data);
        $careerTranslation = $career->translations()->get();
        $career->translations = $careerTranslation;

        return $career;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Career');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Career IN Career CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
