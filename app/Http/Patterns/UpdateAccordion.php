<?php
namespace App\Http\Patterns;

use App\Models\Accordion;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateAccordion implements IOperations
{
    public  function doOperation(array $data)
    {
        $accordion = Accordion::find($data['id']);

        $accordion->update($data['data']);
        $accordionTranslation = $accordion->translations()->get();
        $accordion->translations = $accordionTranslation;

        return $accordion;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Accordion');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Accordion IN Accordion CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
