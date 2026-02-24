<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Lunar\Base\Traits\HasMedia;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Lunar\Base\BaseModel;

class Pharmacy extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'city',
        'phone',
        'address',
    ];
}
