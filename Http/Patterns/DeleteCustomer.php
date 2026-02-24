<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 6:15 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Customer;
// use Lunar\Models\Customer;
use App\Models\Logs;
use Illuminate\Support\Facades\Log;

class DeleteCustomer implements IOperations
{
    public function doOperation(array $data)
    {
        $customer = Customer::where('id',$data['id'])->first();

        if ($customer->users) {
            $ids = $customer->users()->get()->pluck('id')->toArray();
            $customer->users()->whereIn('id',$ids);
        }
        $customer->users()->delete();
        return $customer;
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Customer');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Customer us IN Customer CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
