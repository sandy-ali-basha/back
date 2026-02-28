<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PharmacyService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\SuccessResponse;
use App\Http\Response\ErrorResponse;
use App\Http\Resources\PharmacyResource;
use App\Http\Resources\PharmacyCollection;
use App\Http\Data\AddPharmacyData;
use App\Http\Data\UpdatePharmacyData;

class PharmacyController extends Controller
{
    public function __construct(
        public PharmacyService $pharmacyService
    ) {}

    public function index()
    {
        $pharmacies = $this->pharmacyService->getAll();
        return response()->success(
            new SuccessResponse(
                new PharmacyCollection($pharmacies),
                Response::HTTP_OK
            )
        );
    }

    public function store(Request $request)
    {
        $data = AddPharmacyData::from($request);
        $pharmacy = $this->pharmacyService->create($data);

        return response()->success(
            new SuccessResponse(
                new PharmacyResource($pharmacy),
                Response::HTTP_OK
            )
        );
    }

    public function show($id)
    {
        $pharmacy = $this->pharmacyService->getById($id);

        if (!$pharmacy) {
            return response()->error(
                new ErrorResponse('Pharmacy not found', Response::HTTP_NOT_FOUND)
            );
        }

        return response()->success(
            new SuccessResponse(
                new PharmacyResource($pharmacy),
                Response::HTTP_OK
            )
        );
    }

    public function update(Request $request, $id)
    {
        $data = UpdatePharmacyData::from($request);
        $pharmacy = $this->pharmacyService->update($id, $data);

        return response()->success(
            new SuccessResponse(
                new PharmacyResource($pharmacy),
                Response::HTTP_OK
            )
        );
    }

    public function destroy($id)
    {
        $this->pharmacyService->delete($id);

        return response()->success(
            new SuccessResponse('Pharmacy deleted', Response::HTTP_OK)
        );
    }
}