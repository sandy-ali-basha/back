<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AddressService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Http\Data\AddAddressData;
use App\Http\Data\UpdateAddressData;
class AddressController extends Controller
{

   public AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $customer          = $this->addressService->getCustomerByUserId($userId);

        if (!$customer) {
            $response = new ErrorResponse('Customer not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $addresses = $this->addressService->getAllAddresses($customer);
        $addressResource = new AddressCollection($addresses);
        $response = new SuccessResponse($addressResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddAddressData::from($request);
        
        $data->user_id =  Auth::user()->id;
        $address = $this->addressService->createAddress($data);
        $address->withTranslation= true;
        $addressResource = new AddressResource($address);
        $response = new SuccessResponse($addressResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function show($id,Request $request)
    {
        $addressService = $this->addressService->getById($id);
        if (!$addressService) {
            $response = new ErrorResponse('Address not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $addressService->withTranslation = $request->headers->has('translations');
        $addressServiceResource = new AddressResource($addressService);
        $response = new SuccessResponse($addressServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data = UpdateAddressData::from($request);
        $data->user_id = Auth::user()->id;
        $addressService = $this->addressService->updateAddress($id, $data);
        $addressService->withTranslation= true;
        $addressServiceResource = new AddressResource($addressService);
        $response = new SuccessResponse($addressServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $this->addressService->deleteAddress($id);
        $response = new SuccessResponse("address is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
