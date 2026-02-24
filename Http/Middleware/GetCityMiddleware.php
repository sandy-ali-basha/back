<?php

namespace App\Http\Middleware;

use App\Models\City;
use Closure;
use ErrorException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('city')) {
            $city = City::where('id', $request->header('city'))->first();
            if (!session()->isStarted()) {
                session()->start();
            }
            if ($city) {
                session()->put('city_id', $city->id);
            } else {
                session()->put('city_id', 1);
            }
        }
        return $next($request);
    }
}
