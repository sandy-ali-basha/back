<?php
namespace App\Http\Patterns;

use Lunar\Models\Address;
use App\Models\Logs;
use App\Models\AddressModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class UpdateAddress implements IOperations
{
    public  function doOperation(array $data)
    {
        unset($data['data']['user_id']);
        $address = AddressModel::find($data['id']);
        if ($data['data']['billing_default']||$data['data']['shipping_default']){
            AddressModel::where('customer_id',$data['data']['customer_id'])->update([
                'billing_default'=>false,
                'shipping_default'=>false,
            ]);
        }
        $address->update($data['data']);

        return $address;
    }

    public function audit()
    {
        Logs::createNewRecord('Update', 'Address');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Update Address IN Address CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
