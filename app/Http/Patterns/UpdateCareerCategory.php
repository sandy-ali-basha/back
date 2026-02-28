<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:11 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 XYZ
 */

namespace App\Http\Patterns;

use App\Models\CareerCategory;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class UpdateCareerCategory implements IOperations
{
    public  function doOperation(array $data)
    {
        $careerCategory = CareerCategory::find($data['id']);
        $careerCategory->update($data['data']);
        $careerCategoryTranslation = $careerCategory->translations()->get();
        $careerCategory->translations = $careerCategoryTranslation;

        return $careerCategory;
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
