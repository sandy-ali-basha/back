<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use App\Models\LocationModelTranslation;
class Location extends Model
{
    use HasFactory;
    use Translatable;

    protected $table = 'locations';
    protected $translationModel = LocationModelTranslation::class; 
    public $translatedAttributes = ['office_name', 'address'];

    public $timestamps = true;
    
    protected $fillable = [
        'map_url',
        'is_main',
    ];
    public function translations()
    {
        return $this->hasMany(LocationModelTranslation::class);
    }
    

}
