<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerCategory extends Model
{
    use HasFactory,Translatable;

    protected $fillable = ['name'];
    public $translatedAttributes = ['name'];
    public function setDefaultLocale(?string $locale)
    {
        $this->defaultLocale = 'en';

        return $this;
    }
    public function careers()
    {
        return $this->hasMany(Career::class,'category_id');
    }
    public function getCareerCatById(string $id)
    {
        $careerCategory = $this->where('id','=',$id)->first();
        $careerCategoryTranslation = $careerCategory->translations()->get();
        $careerCategory->translations = $careerCategoryTranslation;
        return $careerCategory;
    }
}
