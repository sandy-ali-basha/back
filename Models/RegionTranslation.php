<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use App\Models\Region;


class RegionTranslation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table ="region_translations";
    protected $fillable = ['name','locale','region_id'];
    protected $hidden = ["updated_at","created_at"];
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id','id');
    }
    
    public function getNameAttribute($value)
    {
        return $value ?? $this->region?->name;
    }
    
}
