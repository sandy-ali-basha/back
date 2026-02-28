<?php

namespace App\Http\Controllers;


use App\Http\Data\UpdateBrandData;
use App\Http\Data\UpdateCitiesData;
use App\Http\Resources\BrandResource;
use App\Http\Resources\StateCollection;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\City;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Lunar\Models\Product;
use Lunar\Models\ProductVariant;
use Nnjeim\World\Models\State;
use Symfony\Component\HttpFoundation\Response;

class CitiesController extends Controller
{

    public function index(){
        $cities = State::all();
        $citiesResource = new StateCollection($cities);
  
        $response = new SuccessResponse($citiesResource, 200);
        return response()->success($response);
    }


    public function update(Request $request)
    {
        $data                   = UpdateCitiesData::from($request);

        foreach ($data->data as $key => $value) {
            $state = City::find($value['id']);
            $state->update([
                'shipping_price' => $value['shipping_price'],
                'name'=>$value['name'],
                'inv_name'=>$value['inv_name'],
                'currency_id'=>$value['currency_id']

            ]);

        }
        $cities = State::all();
        $citiesCollection          = new StateCollection($cities);
        $response               = new SuccessResponse($citiesCollection, Response::HTTP_OK);

        return response()->success($response);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|array',
            'data.*.name' => 'required|string',
            'data.*.inv_name' => 'string',
            'data.*.shipping_price' => 'required|numeric',
            'data.*.currency_id' => 'required|integer',
        ]);

        $cities = [];

        foreach ($validated['data'] as $item) {
            $cities[] = City::create([
                'name' => $item['name'],
                'value' => $item['inv_name'],
                'shipping_price' => $item['shipping_price'],
                'inv_name' => $item['inv_name'],
                'currency_id' => $item['currency_id'],
            ]);
        }

        return response()->success(new SuccessResponse($cities, 200));
    }
  public function destroy($id)
{
    $city = City::find($id);

    if (!$city) {
        $response = new ErrorResponse('Inventory not found', Response::HTTP_NOT_FOUND);
        return response()->error($response);
    }

    $city->delete();

    // Pass an empty array instead of null
    $response = new SuccessResponse([], Response::HTTP_OK, 'Inventory deleted successfully');
    return response()->success($response);
}



}
