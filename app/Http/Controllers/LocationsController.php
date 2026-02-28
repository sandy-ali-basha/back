<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocationsService; // السيرفس الجديد الخاص بالـ contact
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\LocationCollection;
use App\Http\Resources\LocationResource;
use App\Http\Data\AddLocationData;
use App\Http\Data\UpdateLocationData;

class LocationsController extends Controller
{   
    public LocationsService $locationsService;

    public function __construct(LocationsService $locationsService)
    {
        $this->LocationsService = $locationsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = $this->LocationsService->getAll_Locations();
        $locationResource =  LocationResource::collection($locations);
        $response = new SuccessResponse($locationResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $data = AddLocationData::from($request);
        $location = $this->LocationsService->createLocation($data);

        $location->withTranslation = true;

        $locationResource = new LocationResource($location);
        $response = new SuccessResponse($locationResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $location = $this->LocationsService->getById($id);
        $location->withTranslation = true;

        if (!$location) {
            $response = new ErrorResponse('Location not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }

        $locationResource = new LocationResource($location);
        $response = new SuccessResponse($locationResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
        $data = UpdateLocationData::from($request);
        $location = $this->LocationsService->updateLocation($id, $data);

        $location->withTranslation = true;

        $locationResource = new LocationResource($location);
        $response = new SuccessResponse($locationResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $this->LocationsService->deleteLocation($id);

        $response = new SuccessResponse("Location is deleted", Response::HTTP_OK);
        return response()->success($response);
    }
}
