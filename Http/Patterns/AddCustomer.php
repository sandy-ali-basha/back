<?php


namespace App\Http\Patterns;

use App\Http\Helpers\Constants;
use App\Models\Logs;
use App\Models\User;
// use Lunar\Models\Customer;
use App\Models\Customer;


class AddCustomer implements IOperations
{

    public function doOperation(array $data)
    {
        $user_id = $data['user_id'];
        unset($data['user_id']);
        $customer = Customer::create($data);
        $customer->users()->sync([$user_id]);
        $user = User::where('id', '=', $user_id)->first();
        $points = $user->increasePoints(0);
        $user->assignRole(Constants::ROLE_CUSTOMER);
        
        return $customer;
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Customer');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Customer IN User CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
