<?php

namespace App\Http\Patterns;

use App\Models\Pharmacy;

class AddPharmacy implements IOperations
{
    public function doOperation(array $data)
    {
        return Pharmacy::create($data);
    }

    public function audit() {}
    public function getErrorMessage() {}
    public function returnPage() {}
}

