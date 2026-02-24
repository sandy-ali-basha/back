<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:07 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\CareerCategory;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class DeleteCareerCategory implements IOperations
{
    public function doOperation(array $data)
    {
        $career = CareerCategory::find($data['id']);
        $career->deleteTranslations();
        return $career->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'User');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW career category us IN CareerCategory CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
