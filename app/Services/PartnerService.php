<?php 

namespace App\Services;

use App\Models\Partner;
use App\Http\Data\AddPartnerData;
use App\Http\Data\UpdatePartnerData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class PartnerService
{
    public function getAllPartners() : Collection
    {

        return Partner::with('media')->get();

    }

    public function getById($id)
    {
        return Partner::find($id);
    }

    public function createPartner(AddPartnerData $data)
    {
        $partner = Partner::create((array)$data);
        if ($data->logo_url) {
        $partner
            ->addMedia($data->logo_url)
            ->toMediaCollection('partners', 'settings_files');
    }

    $partner->refresh();

    return $partner;
    }


       public function updatePartner($id, UpdatePartnerData $data)
    {
        $partner = Partner::findOrFail($id);
    
        if ($data->logo_url) {
            $partner
                ->clearMediaCollection('partners')
                ->addMedia($data->logo_url)
                ->toMediaCollection('partners', 'settings_files');
        }
    
        if ($data->name) {
            $partner->update(['name' => $data->name]);
        }
    
        $partner->refresh();
    
        return $partner;
    }


    public function deletePartner($id)
    {
        $Partner = Partner::findOrFail($id);
        $Partner->delete();
    }
}
