<?php

namespace App\Http\Patterns;

use App\Models\Pharmacy;

class UpdatePharmacy implements IOperations
{
    public function doOperation(array $data)
    {
        $pharmacy = Pharmacy::findOrFail($data['id']);

        $updateData = array_filter(
            $data['data'],
            fn ($value) => !is_null($value)
        );

        $pharmacy->update($updateData);

        return $pharmacy;
    }

    public function audit() {}
    public function getErrorMessage() {}
    public function returnPage() {}
}

