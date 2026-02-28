<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AboutUs;


class AboutUsModelTranslation extends Model
{
    public $timestamps = true;
    protected $fillable = ['id', 'about_us_id','description','title'];
    
    protected $table = 'about_us_model_translation';
    public function aboutUs()
    {
        return $this->belongsTo(AboutUs::class, 'about_us_id','id');
    }
     // Override getAttribute للـ title
    public function getTitleAttribute($value)
    {
        return $value ?? $this->aboutUs?->title;
    }

    public function getDescriptionAttribute($value)
    {
        return $value ?? $this->aboutUs?->description;
    }
     public function getImageUrlAttribute($value) 
    {
        return $value ?? $this->aboutUs?->image_url;
    }
     public function getSectionAttribute($value)
    {
        return $value ?? $this->aboutUs?->section;
    }
}
