<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Currency;
use Astrotomic\Translatable\Translatable;
use App\Models\RegionTranslation; 


class Region extends Model
{
    use HasFactory, Translatable;
    protected $table = "regions";
    protected $fillable = ['name'];
    public $translatedAttributes = ['name'];
    protected $hidden = [
        "translations",
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $appends =["name_ar","name_en","name_kr"];
    // Relationship with City

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function getNameArAttribute()
    {
        return $this->translate('ar')?->name;
    }

    public function getNameEnAttribute()
    {
        return $this->translate('en')?->name;
    }

    public function getNameKrAttribute()
    {
        return $this->translate('kr')?->name;
    }
    

}
