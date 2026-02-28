<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/24
 * Time: 7:35 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Http\Helpers\Constants;
use App\Models\Logs;
use App\Models\TermsAndConditions;
use Illuminate\Support\Facades\Log;

class AddTerms implements IOperations
{

    public function doOperation(array $data)
    {
        $term = TermsAndConditions::create($data);

        $termTranslation = $term->translations()->get();
        $term->translations = $termTranslation;

        return $term;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'termsAndConditions');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD Website  Settings IN TermsAndConditions CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
