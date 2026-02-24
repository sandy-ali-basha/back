<?php

namespace App\Services;

use App\Models\Pharmacy;
use App\Http\Patterns\AddPharmacy;
use App\Http\Patterns\UpdatePharmacy;
use App\Http\Patterns\DeletePharmacy;
use Illuminate\Database\Eloquent\Collection;

class PharmacyService
{
    public function getAll(): Collection
    {
        return Pharmacy::all();
    }

    public function create($data)
    {
        return (new AddPharmacy())->doOperation($data->toArray());
    }

    public function getById($id)
    {
        return Pharmacy::find($id);
    }

    public function update($id, $data)
    {
        return (new UpdatePharmacy())->doOperation([
            'id' => $id,
            'data' => $data->toArray()
        ]);
    }

    public function delete($id)
    {
        return (new DeletePharmacy())->doOperation(['id' => $id]);
    }
}
