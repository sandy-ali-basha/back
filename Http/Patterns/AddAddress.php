<?php

namespace App\Http\Patterns;

use Lunar\Facades\DB;
use Lunar\Models\Address;
use App\Models\Logs;
use App\Models\AddressModel;
use Illuminate\Support\Facades\Log;

class AddAddress implements IOperations
{
    public  function doOperation(array $data)
    {
        unset($data['user_id']);
        if ($data['billing_default']||$data['shipping_default']){
            AddressModel::where('customer_id',$data['customer_id'])->update([
                'billing_default'=>false,
                'shipping_default'=>false,

            ]);
        }
        return AddressModel::create($data);
    }

    public function audit()
    {
        Logs::createNewRecord('Add', 'Address');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD Address IN Address CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
