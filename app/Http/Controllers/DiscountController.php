<?php

namespace App\Http\Controllers;

use App\Http\Data\AddDiscountData;
use App\Http\Data\UpdateDiscountData;
use App\Http\Helpers\CodeGenerator;
use App\Http\Resources\DiscountCollection;
use App\Http\Resources\DiscountResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountController extends Controller
{
    public DiscountService $service;

    public function __construct(DiscountService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $dicsounts        = $this->service->getAllDiscounts();
        $discountResource = new DiscountCollection($dicsounts);
        $response      = new SuccessResponse($discountResource, Response::HTTP_OK);

        return response()->success($response);
    }

    // Store a newly created brand
    public function store(Request $request)
    {
        $request->merge(['type' => '', 'handle' => '', 'coupon' => '']);
        $data                   = AddDiscountData::from($request);
        $data->type = 'App\DiscountTypes\CustomAmountOff';
        $data->handle = implode('_', explode(' ', $data->name))."_".now()->timestamp.'_'.random_int(1,99999);
        $data->coupon = CodeGenerator::genererate(9);
        $data->uses = 0;
        $discount = $this->service->createDiscount($data);
        //$discount->withTranslation = true;
        $discountResource = new DiscountResource($discount);
        $response = new SuccessResponse($discountResource, Response::HTTP_OK);

        return response()->success($response);
    }

   
    // Display the specified brand
    public function show($id, Request $request)
    {
        $discount = $this->service->getById($id);
        if (!$discount) {
            $response = new ErrorResponse('Discount not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        // $discount->withTranslation = $request->headers->has('translations');
        $discountResource          = new DiscountResource($discount);
        $response               = new SuccessResponse($discountResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function destroy($id)
    {
        $discount = $this->service->getById($id);
        if (!$discount) {
            $response = new ErrorResponse('discount not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        $this->service->delete($id);
        $response = new SuccessResponse("discount is deleted", Response::HTTP_OK);

        return response()->success($response);
    }


    public function update(Request $request, $id)
    {
        $data                   = UpdateDiscountData::from($request);
        $discount                  = $this->service->updateDiscount($id, $data);
        // $discount->withTranslation = true;
        $discountResource          = new DiscountResource($discount);
        $response               = new SuccessResponse($discountResource, Response::HTTP_OK);

        return response()->success($response);
    }
}
