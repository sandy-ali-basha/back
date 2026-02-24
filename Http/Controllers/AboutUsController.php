<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AboutUsService;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Http\Resources\AboutUsCollection;
use App\Http\Resources\AboutUsResource;
use App\Http\Data\AddAboutUsData;
use App\Http\Data\UpdateAboutUsData;
use Illuminate\Support\Facades\App;

class AboutUsController extends Controller
{

   public AboutUsService $aboutusService;

    public function __construct(AboutUsService $aboutusService)
    {
        $this->aboutusService = $aboutusService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      
        $aboutuss = $this->aboutusService->getAllAboutUss();
        $aboutusResource = AboutUsResource::collection($aboutuss);
        $response = new SuccessResponse($aboutusResource, Response::HTTP_OK);
        return response()->success($response);
        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data =  AddAboutUsData::from($request);

        $aboutus = $this->aboutusService->createAboutUs($data);
        $aboutus->withTranslation= true;
        $aboutusResource = new AboutUsResource($aboutus);

        $response = new SuccessResponse($aboutusResource, Response::HTTP_OK);
        return response()->success($response);
    }
    


    public function show($id,Request $request)
    {
        $aboutusService = $this->aboutusService->getById($id);
        
        if (!$aboutusService) {
            $response = new ErrorResponse('AboutUs not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }
         $aboutusService->withTranslation = $request->headers->has('translations');
        $aboutusServiceResource = AboutUsResource::collection($aboutusService);
        $response = new SuccessResponse($aboutusServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function update(Request $request, $id)
    {

        $data = UpdateAboutUsData::from($request);
        $aboutusService = $this->aboutusService->updateAboutUs($id, $data);
        $aboutusService->withTranslation= true;
        $aboutusServiceResource = new AboutUsResource($aboutusService);
        $response = new SuccessResponse($aboutusServiceResource, Response::HTTP_OK);
        return response()->success($response);
    }


    public function destroy($id)
    {
        $this->aboutusService->deleteAboutUs($id);
        $response = new SuccessResponse("aboutus is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

}
