<?php
// app/Http/Controllers/RegionController.php

namespace App\Http\Controllers;

use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\City;
use App\Models\CurrencyModel;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\RegionTranslation;
use Symfony\Component\HttpFoundation\Response;

class RegionController extends Controller
{
    // Display a list of regions with their linked cities
    public function index()
    {
        $regions = Region::with(['cities', 'currency'])->get();
        $response = new SuccessResponse($regions, Response::HTTP_OK);
        return response()->success($response);
    }

    // Store a new region
    public function store(Request $request)
    {
        $request->validate([
        'name.en' => 'required|string|max:255',
        'name.ar' => 'nullable|string|max:255',
        'name.kr' => 'nullable|string|max:255'
    ]);

    $region = Region::create([
        'currency_id'    => $request->currency_id,
        'shipping_price' => $request->shipping_price,
        'inv_name'       => $request->inv_name,
    ]);
    // إضافة الترجمات
    foreach ($request->name as $locale => $value) {
        if ($value) {
            $region->translateOrNew($locale)->name = $value;
        }
    }

    $region->save();

    $response = new SuccessResponse(
        ['region' => $region, 'message' => 'Region created successfully.'],
        Response::HTTP_OK
    );

    return response()->success($response);

    }

    // Show a specific region with its cities
    public function show($id)
    {
        $region = Region::with(['cities','currency'])->findOrFail($id);
        if (!$region) {
            return response()->error(new ErrorResponse('Region not found', Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        $response      = new SuccessResponse( $region, Response::HTTP_OK);

        return response()->success($response);
    }

    // Update an existing region
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $region = Region::findOrFail($id);
        $region->update(['name' => $request->name,'currency_id'=>$request->currency_id,'shipping_price'=>$request->shipping_price,'inv_name'=>$request->inv_name,]);

        $response      = new SuccessResponse(['region' => $region, 'message' => 'Region updated successfully.'], Response::HTTP_OK);
        return response()->success($response);    }

    public function destroy($id)
    {
        $region = Region::findOrFail($id);
        if (!$region) {
            return response()->error(new ErrorResponse('Region not found', Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        $region->delete();

        $response      = new SuccessResponse(['message' => 'Region deleted successfully.'], Response::HTTP_OK);
        return response()->success($response);
    }


    public function createCity(Request $request)
    {
        $request->validate([
            // Name should be required
            'name' => 'required|string|max:255',
            'inv_name' => 'required|string|max:255',
            // Assuming shipping_price is required on the City model
            'shipping_price' => 'required|integer',
            // Validate currency_id exists in the database
            'currency_id' => 'required|integer|exists:lunar_currencies,id',
        ]);

        // 1. Create the new City (assuming City model has fillables for these fields)
        $city                 = new City();
        $city->name           = $request->name;
        $city->name_ar           = $request->name_ar;
        $city->name_kr           = $request->name_kr;
        $city->inv_name           = $request->inv_name;
        $city->shipping_price = $request->shipping_price;
        $city->save();

        // 2. Attach the new city to the specified currency using the pivot table
        $currency = CurrencyModel::findOrFail($request->currency_id);
        $currency->cities()->attach($city->id);

        $response = new SuccessResponse(['city' => $city, 'message' => 'City created and linked to currency successfully.'], Response::HTTP_CREATED);
        return response()->success($response);
    }
    public function updateCities(Request $request, $id)
    {
        $request->validate([
            'cities' => 'array',
            'cities.*' => 'integer|exists:world_states,id',
        ]);

        $region = Region::findOrFail($id);
        if (!$region) {
            return response()->error(new ErrorResponse('Region not found', Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        City::whereIn('id', $request->cities)->update(['region_id' => $region->id]);

        City::whereNotIn('id', $request->cities)->where('region_id', $region->id)->update(['region_id' => null]);


        $response      = new SuccessResponse(['message' => 'Cities updated successfully'], Response::HTTP_OK);
        return response()->success($response);
    }

}
