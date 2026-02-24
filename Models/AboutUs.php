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

class AboutUs  extends BaseModel implements SpatieHasMedia
{
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('about_us');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
    use HasFactory, SoftDeletes,Translatable,HasMedia;

    protected $fillable = ['description','title','section'];
    public $translatedAttributes = ['description','title'];
    
    public function category()
    {
        return $this->belongsTo(AboutUs::class);
    }

    public function getAboutUsById(int $id)
    {  
        return AboutUsModelTranslation::where("about_us_id", $id)->get();
    }
     public function getImageUrlAttribute()
    {
        return $this->getFirstMediaUrl('about_us') 
            ?: asset('images/default-partner.png');
    }
}
