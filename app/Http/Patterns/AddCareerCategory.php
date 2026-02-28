<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:01 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\CareerCategory;
use App\Models\Logs;

class AddCareerCategory implements IOperations
{

    public  function doOperation(array $data)
    {
        $careerCategory= CareerCategory::create($data);
        $careerCategoryTranslation = $careerCategory->translations()->get();

        $careerCategory->translations = $careerCategoryTranslation;
        return $careerCategory;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Career Category');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Career Category IN Career Category CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
