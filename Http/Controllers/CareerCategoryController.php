<?php

namespace App\Http\Controllers;

use App\Http\Data\AddCareerCategoryData;
use App\Http\Data\UpdateCareerCategoryData;
use App\Http\Resources\CareerCategoryCollection;
use App\Http\Resources\CareerCategoryResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\CareerCategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CareerCategoryController extends Controller
{

    public CareerCategoryService $careerCategoryService;

    public function __construct(CareerCategoryService $careerCategoryService)
    {
        $this->careerCategoryService = $careerCategoryService;

    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careersCategories = $this->careerCategoryService->getAllCareersCategories();
        $careersCategoriesCollection = new CareerCategoryCollection($careersCategories);

        $response       = new SuccessResponse($careersCategoriesCollection, Response::HTTP_OK);

        return response()->success($response);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $careerCategory = AddCareerCategoryData::from($request);
        $careerCategory = $this->careerCategoryService->saveCareerCategory($careerCategory);
        $careerCategory->withTranslation= true;
        $careerResource = new CareerCategoryResource($careerCategory);
        $response       = new SuccessResponse($careerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id,Request $request)
    {
        $careersCategory = $this->careerCategoryService->getCareerById($id);
        $careersCategoryResource = new CareerCategoryResource($careersCategory);
        $careersCategoryResource->withTranslation = $request->headers->has('translations');
        $response       = new SuccessResponse($careersCategoryResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $careerCategory = UpdateCareerCategoryData::from($request);
        $careerCategory = $this->careerCategoryService->updateCareerCategory($id,$careerCategory);
        $careerCategory->withTranslation= true;
        $careerResource = new CareerCategoryResource($careerCategory);
        $response       = new SuccessResponse($careerResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $careersCategory = $this->careerCategoryService->getCareerById($id);
        if (!$careersCategory){
            $response = new ErrorResponse('Category not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if ($careersCategory->careers()->count()!=0){
            $response = new ErrorResponse("Can't delete category with related careers", Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $this->careerCategoryService->deleteCareerCategory($id);
        $response       = new SuccessResponse("career category is deleted", Response::HTTP_OK);

        return response()->success($response);
    }
}
