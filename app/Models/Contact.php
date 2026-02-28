<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use App\Models\LocationModelTranslation;
use Illuminate\Support\Facades\Config;

class Contact extends Model
{
    use HasFactory;
    protected $table ="contacts";
    protected $appends = ['locations'];

    
    protected $fillable = [
        'companyName',
        'facebook',
        'instagram',
        'linkedin',
        'whatsapp',
        'email',
    ];

    public function getLocationsAttribute()
    {
        $locale = request()->header('locale');

        if($locale)
        return LocationModelTranslation::where('locale', $locale)->get();
        else 
        return LocationModelTranslation::all();
        
    }
}
