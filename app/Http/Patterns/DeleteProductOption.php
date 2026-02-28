<?php
namespace App\Http\Patterns;;

use Lunar\Models\ProductOption;
use App\Models\Logs;
use App\Models\ProductOptionModel;
use Illuminate\Support\Facades\Log;

class DeleteProductOption implements IOperations
{
    public function doOperation(array $data)
    {
        $productOption = ProductOption::find($data['id']);
        $productOption->values()->delete();
        return $productOption->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'ProductOption');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN Deleting NEW ProductOption us IN ProductOption CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
