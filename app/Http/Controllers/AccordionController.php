<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccordionService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\AccordionCollection;
use App\Http\Resources\AccordionResource;
use App\Http\Data\AddAccordionData;
use App\Http\Data\UpdateAccordionData;
class AccordionController extends Controller
{

   public AccordionService $accordionService;

    public function __construct(AccordionService $accordionService)
    {
        $this->accordionService = $accordionService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(int $product_id,Request $request)
    {
        $accordions = $this->accordionService->getAllAccordions($product_id);
        
        $accordions->withTranslation = $request->headers->has('translations');
        $accordionResource = new AccordionCollection($accordions);
        $response = new SuccessResponse($accordionResource, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddAccordionData::from($request);
        $accordion = $this->accordionService->createAccordion($data);
        $accordion->withTranslation= true;
        $accordionResource = new AccordionResource($accordion);
        $response = new SuccessResponse($accordionResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function show($id,Request $request)
    {
        $accordionService = $this->accordionService->getById($id);
        if (!$accordionService) {
            $response = new ErrorResponse('Accordion not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $accordionService->withTranslation = $request->headers->has('translations');
        $accordionServiceResource = new AccordionResource($accordionService);
        $response = new SuccessResponse($accordionServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data = UpdateAccordionData::from($request);
        $accordionService = $this->accordionService->updateAccordion($id, $data);
        $accordionService->withTranslation= true;
        $accordionServiceResource = new AccordionResource($accordionService);
        $response = new SuccessResponse($accordionServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $accordionService = $this->accordionService->getById($id);
        if (!$accordionService) {
            $response = new ErrorResponse('Accordion not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
        $this->accordionService->deleteAccordion($id);
        $response = new SuccessResponse("accordion is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
