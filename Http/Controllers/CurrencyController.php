<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Lunar\Models\Currency;
use Symfony\Component\HttpFoundation\Response;

class CurrencyController extends Controller
{
    /**
     * Display a listing of all Lunar currencies.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Retrieve all currencies from the Lunar database
        $currencies = Currency::all();
        $response = new SuccessResponse($currencies, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Store a newly created currency in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // Code must be unique and a maximum of 3 characters (standard ISO 4217)
            'code' => 'required|string|max:3|unique:lunar_currencies,code',
            'decimal_places' => 'required|integer|min:0|max:4',
            'enabled' => 'nullable|boolean',
            'exchange_rate' => 'required',
        ]);

        $currency = Currency::create($request->only([
            'name',
            'code',
            'decimal_places',
            'enabled',
            'exchange_rate'
        ]));

        $response = new SuccessResponse(['currency' => $currency, 'message' => 'Currency created successfully.'], Response::HTTP_CREATED);
        return response()->success($response);
    }

    /**
     * Display the specified currency.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Use findOrFail to leverage Laravel's exception handling for 404
        $currency = Currency::findOrFail($id);

        $response = new SuccessResponse($currency, Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Update the specified currency in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Find the currency first
        $currency = Currency::findOrFail($id);

        $request->validate([
            'name' => 'string|max:255',
            // Ensure the code is unique, but ignore the current currency's ID
            'decimal_places' => 'integer|min:0|max:4',
            'enabled' => 'nullable|boolean',
            'exchange_rate' => 'nullable',

        ]);

        $currency->update($request->only([
            'name',
            'code',
            'decimal_places',
            'enabled',
            'exchange_rate'

        ]));

        $response = new SuccessResponse(['currency' => $currency, 'message' => 'Currency updated successfully.'], Response::HTTP_OK);
        return response()->success($response);
    }

    /**
     * Remove the specified currency from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);
        $currency->delete();

        $response = new SuccessResponse(['message' => 'Currency deleted successfully.'], Response::HTTP_OK);
        return response()->success($response);
    }
}
