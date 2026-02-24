<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:23 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Career;
use App\Models\Logs;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class UpdateCareer implements IOperations
{
    public  function doOperation(array $data)
    {
        $career = Career::find($data['id']);

        $career->update($data['data']);
        $careerTranslation = $career->translations()->get();
        $career->translations = $careerTranslation;

        return $career;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Career');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Career IN Career Category CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
