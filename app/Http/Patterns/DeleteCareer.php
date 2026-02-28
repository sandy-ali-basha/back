<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 5:56 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Career;
use App\Models\Logs;

class DeleteCareer implements IOperations
{
    public function doOperation(array $data)
    {
        $career = Career::find($data['id']);
        $career->deleteTranslations();
        return $career->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('Delete', 'User');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Delete Career us IN career CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
