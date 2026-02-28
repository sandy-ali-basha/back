<?php
namespace App\Http\Patterns;;

use Lunar\Models\Address;
use App\Models\Logs;
use App\Models\AddressModel;
use Illuminate\Support\Facades\Log;

class DeleteAddress implements IOperations
{
    public function doOperation(array $data)
    {
        $address = Address::find($data['id']);
        return $address->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Address');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Address us IN Address CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
