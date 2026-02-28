<?php

namespace App\Services;

use App\Http\Data\UpdateCustomerData;
use App\Http\Patterns\AddCustomer;
use App\Http\Patterns\DeleteCustomer;
use App\Http\Patterns\UpdateCustomer;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Customer;

class CustomerService
{
    protected Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function createNewCustomer($customerData)
    {
        $customer = new AddCustomer();

        return $customer->doOperation($customerData->toArray());
    }

    public function getAllCustomers(): Collection
    {
        return Customer::all();
    }

    public function getByUserId($id)
    {
        return Customer::whereHas('users', function($q) use($id) {
            $q->whereIn('users.id', [$id]);
        })->first();
    }
    
    public function getById($id)
    {
        return Customer::where('id', $id)->first();
    }

    public function update($customer, UpdateCustomerData $data)
    {
        $updateCustomer = new UpdateCustomer();

        return $updateCustomer->doOperation(['id'=>$customer->id,'data'=>$data->toArray()]);
    }

    public function delete($id)
    {
        $updateCustomer = new DeleteCustomer();

        return $updateCustomer->doOperation(['id'=>$id]);
    }

}
