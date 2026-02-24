<?php

namespace App\Http\Controllers;

use App\Http\Data\UpdateSettingsData;
use App\Http\Resources\SettingCollection;
use App\Http\Resources\SettingResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    public SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        $settings        = $this->settingService->getAllSetings();
        $brandResource = new SettingCollection($settings);
        $response      = new SuccessResponse($brandResource, Response::HTTP_OK);

        return response()->success($response);
    }

    

    // Display the specified brand
    public function show($name, Request $request)
    {
        $setting = $this->settingService->getByName($name);
        if (!$setting) {
            $response = new ErrorResponse('Setting not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $settingResource          = new SettingResource($setting);
        $response               = new SuccessResponse($settingResource, Response::HTTP_OK);

        return response()->success($response);
    }

    
    public function update(Request $request)
    {
        $data                   = UpdateSettingsData::from($request);
        try {
            DB::beginTransaction();
            $settings                  = $this->settingService->updateSettings($data);
            if (count($settings) < 1) {
                $response = new ErrorResponse("No Settings were found.", Response::HTTP_UNPROCESSABLE_ENTITY);
                return response()->error($response);
            }
            DB::commit();
            $settingsCollection          = new SettingCollection($settings);
            $response               = new SuccessResponse($settingsCollection, Response::HTTP_OK);

            return response()->success($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $response = new ErrorResponse($e->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->error($response);
        }
    }

  
}
