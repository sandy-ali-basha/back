<?php

namespace App\Http\Patterns;

use App\Models\Accordion;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class AddAccordion implements IOperations
{
    public  function doOperation(array $data)
    {
        $accordion = Accordion::create($data);
        $accordionTranslation = $accordion->translations()->get();
        $accordion->translations = $accordionTranslation;

        return $accordion;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Accordion');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Accordion IN Accordion CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
