<?php

namespace App\Http\Controllers;


use App\Http\Data\AddTermsData;
use App\Http\Data\UpdateTermsData;
use App\Http\Resources\TermsAndConditionsCollection;
use App\Http\Resources\TermsAndConditionsResource;
use App\Http\Response\SuccessResponse;
use App\Models\TermsAndConditions;
use App\Services\WebsiteSettingsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebsiteSettingsController extends Controller
{

    public WebsiteSettingsService $websiteSettingsService;

    public function __construct(WebsiteSettingsService $websiteSettingsService)
    {
        $this->websiteSettingsService = $websiteSettingsService;

    }
    public function index()
    {
        $terms = $this->websiteSettingsService->getAllTerms();
        $termsCollection = new TermsAndConditionsCollection($terms);
        $response       = new SuccessResponse($termsCollection, Response::HTTP_OK);

        return response()->success($response);
    }
    /**
     * Display a listing of the resource.
     */
    public function getTermsAndConditions(Request $request,  int $id)
    {
        $terms         = $this->websiteSettingsService->getTerms($id);
        $terms->withTranslation = $request->headers->has('translations');
        $termsResource = new TermsAndConditionsResource($terms);
        $response      = new SuccessResponse($termsResource, Response::HTTP_OK);

        return response()->success($response);
    }


    /**
     * Update the specified resource in storage.
     */
    public function updateTermsAndConditions(Request $request, int $id)
    {
        $terms = UpdateTermsData::from($request);
        $terms = $this->websiteSettingsService->updateTerms($terms,$id);
        $terms->withTranslation= true;
        $termsResource = new TermsAndConditionsResource($terms);
        $response       = new SuccessResponse($termsResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function storeTermsAndConditions(Request $request)
    {
        $terms = AddTermsData::from($request);
        $terms = $this->websiteSettingsService->createTerms($terms);
        $terms->withTranslation= true;
        $termsResource = new TermsAndConditionsResource($terms);
        $response       = new SuccessResponse($termsResource, Response::HTTP_OK);

        return response()->success($response);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->websiteSettingsService->deleteTerm($id);
        $response = new SuccessResponse("term is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
}
