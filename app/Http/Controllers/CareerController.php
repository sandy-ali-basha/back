<?php

namespace App\Http\Controllers;

use App\Http\Data\AddCareerData;
use App\Http\Data\UpdateCareerData;
use App\Http\Resources\AllCareerResource;
use App\Http\Resources\CareerCollection;
use App\Http\Resources\CareerResource;
use App\Http\Response\SuccessResponse;
use App\Services\CareerService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CareerController extends Controller
{

    public CareerService $careerService;

    public function __construct(CareerService $careerService)
    {
        $this->careerService = $careerService;

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careers          = $this->careerService->getAllCareers();
        $careerCollection = new CareerCollection($careers);
        $response         = new SuccessResponse($careerCollection, Response::HTTP_OK);

        return response()->success($response);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $career         = AddCareerData::from($request);
        $career         = $this->careerService->saveCareer($career);
        $career->withTranslation= true;
        $careerResource = new CareerResource($career);
        $response       = new SuccessResponse($careerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $career                  = $this->careerService->getCareerById($id);
        $career->withTranslation = $request->headers->has('translations');
        $careerResource          = new CareerResource($career);
        $response                = new SuccessResponse($careerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $career         = UpdateCareerData::from($request);
        $career         = $this->careerService->updateCareer($id, $career);
        $career->withTranslation= true;
        $careerResource = new CareerResource($career);
        $response       = new SuccessResponse($careerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->careerService->deleteCareer($id);
        $response = new SuccessResponse("career is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
}
