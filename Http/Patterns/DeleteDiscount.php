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


use App\Models\Logs;
use Lunar\Models\Discount;

class DeleteDiscount implements IOperations
{
    public function doOperation(array $data)
    {
        $discount = Discount::find($data['id']);
        //$discount->deleteTranslations();
        return $discount->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('Delete', 'Discount');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Delete Discount IN Discount CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
