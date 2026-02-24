<?php

namespace App\Services;

use App\Models\Location;
use App\Models\LocationModelTranslation;
use App\Http\Data\AddLocationData;
use App\Http\Data\UpdateLocationData;
use Illuminate\Database\Eloquent\Collection;

class LocationsService
{
    public function getAll_Locations(): Collection
    {
        $locale = request()->header("locale") ?? app()->getLocale();
        if($locale)
        return LocationModelTranslation::where("locale", $locale)->get();
        else
        return LocationModelTranslation::all();
    }

    public function getById($id)
    {
        return Location::with('translations')->find($id);
    }

    public function createLocation(AddLocationData $data)
    {
        $location = Location::create((array) $data);
        if (!empty($data->is_main) && $data->is_main) {
        // نجعل كل السجلات الأخرى false
                Location::where('is_main', 1)
                ->where('id', '!=', $location->id)
                ->update(['is_main' => 0]);
                }
        return $location;
    }

    public function updateLocation($id, UpdateLocationData $data)
    {
        $location = Location::findOrFail($id);
        $location->update((array) $data);
        if (!empty($data->is_main) && $data->is_main) {
        // نجعل كل السجلات الأخرى false
                Location::where('is_main', 1)
                ->where('id', '!=', $location->id)
                ->update(['is_main' => 0]);
                }
        return $location;
    }

    public function deleteLocation($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();
    }
}
