<?php

namespace App\Services;

use App\Http\Patterns\AddAddress;
use App\Http\Patterns\DeleteAddress;
use App\Http\Patterns\UpdateAddress;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Models\Address;
// use Lunar\Models\Customer;
use App\Models\Customer;


class AddressService
{

    protected Address $model;

    public function __construct(Address $model)
    {
        $this->model = $model;
    }

    public function getAllAddresses(Customer $customer): Collection
    {

        return Address::where('customer_id', $customer->id)->get();
    }

    public function createAddress($data)
    {
        
        $customer            = $this->getCustomerByUserId($data->user_id);

        if (!$customer) {
            throw new \Exception("Customer not found for user_id: " . $data->user_id);
        }

        $data                = $data->toArray();
        $data['customer_id'] = $customer->id;
        $address             = new AddAddress();
        return $address->doOperation($data);
    }

    public function getCustomerByUserId(int $userId)
    {
        return Customer::whereHas('users', function($q) use ($userId) {
            $q->whereIn('users.id', [$userId]);
        })->first();
    }

    public function getById($id)
    {
        return $this->model->where('id',$id)->first();
    }

    public function updateAddress($id, $data)
    {
        $customer            = $this->getCustomerByUserId($data->user_id);
        $data                = $data->toArray();
        $data['customer_id'] = $customer->id;
        $address             = new UpdateAddress();

        return $address->doOperation(['id' => $id, 'data' => $data]);
    }

    public function deleteAddress($id)
    {
        $address = new DeleteAddress();

        return $address->doOperation(['id' => $id]);
    }
}
