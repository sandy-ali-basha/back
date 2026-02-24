<?php

namespace App\Http\Controllers;

use App\Http\Data\AddCustomerData;
use App\Http\Data\UpdateCustomerData;
use App\Http\Data\UpdateUserData;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\CustomerService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    public CustomerService $customerService;
    public UserService     $userService;

    public function __construct(UserService $userService, CustomerService $customerService)
    {
        $this->userService     = $userService;
        $this->customerService = $customerService;
    }

    public function index()
    {
        $customers        = $this->customerService->getAllCustomers();
        $customerResource = new CustomerCollection($customers);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }


    // Display the specified customer
    public function show($id)
    {
        $customer = $this->customerService->getById($id);
        if (!$customer) {
            $response = new ErrorResponse('Customer not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $customerResource = new CustomerResource($customer);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    // Update the specified customer
    public function update(Request $request, $id)
    {
        $userData     = UpdateUserData::from($request);

        try {
            DB::beginTransaction();
            $customerData = UpdateCustomerData::from($request);
            $customer = $this->customerService->getByUserId($id);
            $user = $this->userService->getById($id);
            if (!$customer|| !$user) {
                $response = new ErrorResponse('Customer/User not found', Response::HTTP_NOT_FOUND);
                return response()->error($response);
            }
            $customer = $this->customerService->update($customer, $customerData);
            $this->userService->update($user, $userData);
            DB::commit();

        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            $response = new ErrorResponse($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }

        $customerResource = new CustomerResource($customer);
        $response         = new SuccessResponse($customerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function destroy($id)
    {
        $customer = $this->customerService->getById($id);
        if (!$customer) {
            $response = new ErrorResponse('Customer not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $this->customerService->delete($id);
        $response = new SuccessResponse('Customer Deleted', Response::HTTP_OK);

        return response()->success($response);
    }
}
