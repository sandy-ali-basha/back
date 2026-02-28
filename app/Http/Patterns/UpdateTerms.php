<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/07
 * Time: 5:27 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Logs;
use App\Models\TermsAndConditions;
use Illuminate\Support\Facades\Log;

class UpdateTerms implements IOperations
{
    public  function doOperation(array $data)
    {
        $terms = TermsAndConditions::find($data['id']);
        $terms->update($data['data']);
        $termsTranslation = $terms->translations()->get();
        $terms->translations = $termsTranslation;
        return $terms;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Terms');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Terms IN WebsiteSettings CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
