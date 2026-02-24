<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 6:02 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Logs;
use Illuminate\Support\Facades\Log;
// use Lunar\Models\Customer;
use App\Models\Customer;

class UpdateCustomer implements IOperations
{

    public function doOperation(array $data)
    {
        $customer = Customer::find($data['id']);
        unset($data['data']['user_id']);

        $customer->update($data['data']);
        return $customer;

    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Customer');

    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Customer IN Customer CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
