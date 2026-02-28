<?php
namespace App\Http\Patterns;;

use App\Models\Accordion;
use App\Models\Logs;
use App\Models\AccordionModel;
use Illuminate\Support\Facades\Log;

class DeleteAccordion implements IOperations
{
    public function doOperation(array $data)
    {
        $accordion = Accordion::find($data['id']);
        if ($accordion) {
            $accordion->deleteTranslations();
        }
        return $accordion->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Accordion');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Accordion us IN Accordion CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
