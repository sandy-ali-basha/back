<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationModelTranslation extends Model
{
    use HasFactory;
    protected $table = 'location_model_translations';
    public $timestamps = true;
    protected $hidden = ["created_at","updated_at","location_id"];
    
   protected $fillable = [
        'location_id',
        'locale',
        'office_name',
        'address'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
     public function getMapUrlAttribute($value)
    {
        return $value ?? $this->location?->map_url;
    }

    public function getIsMainAttribute($value)
    {
        return $value ?? $this->location?->is_main;
    }
}
