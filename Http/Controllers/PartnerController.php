<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PartnerService; // السيرفس الجديد الخاص بالـ Partner
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\PartnerCollection;
use App\Http\Resources\PartnerResource;
use App\Http\Data\AddPartnerData;
use App\Http\Data\UpdatePartnerData;

class PartnerController extends Controller
{
    public PartnerService $partnerService;

    public function __construct(PartnerService $partnerService)
    {
        $this->PartnerService = $partnerService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = $this->PartnerService->getAllPartners();
        $PartnerResource =  PartnerResource::collection($partners);
        $response = new SuccessResponse($PartnerResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $data = AddPartnerData::from($request);
        $partner = $this->PartnerService->createPartner($data);

        $partner->withTranslation = true;

        $PartnerResource = new PartnerResource($partner);
        $response = new SuccessResponse($PartnerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $partner = $this->PartnerService->getById($id);

        if (!$partner) {
            $response = new ErrorResponse('Partner not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }


        $PartnerResource = new PartnerResource($partner);
        $response = new SuccessResponse($PartnerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource.
     */
    public function update(Request $request, $id)
    {
            $data = UpdatePartnerData::from($request);
        $partner = $this->PartnerService->updatePartner($id, $data);

        $partner->withTranslation = true;

        $PartnerResource = new PartnerResource($partner);
        $response = new SuccessResponse($PartnerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Remove the specified resource.
     */
    public function destroy($id)
    {
        $this->PartnerService->deletePartner($id);

        $response = new SuccessResponse("Partner is deleted", Response::HTTP_OK);
        return response()->success($response);
    }
}
