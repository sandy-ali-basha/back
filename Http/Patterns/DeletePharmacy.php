<?php

namespace App\Http\Patterns;

use App\Models\Pharmacy;

class DeletePharmacy implements IOperations
{
    public function doOperation(array $data)
    {
        return Pharmacy::where('id', $data['id'])->delete();
    }

    public function audit() {}
    public function getErrorMessage() {}
    public function returnPage() {}
}
