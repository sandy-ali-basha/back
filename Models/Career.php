<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Career extends Model
{
    use HasFactory, SoftDeletes,Translatable;

    protected $fillable = [
        'vacancy_name',
        'requisition_no',
        'time_type',
        'location',
        'country',
        'description',
        'about_us',
        'category_id',
    ];
    public $translatedAttributes = ['vacancy_name', 'location','country','description','about_us'];
    public function category()
    {
        return $this->belongsTo(CareerCategory::class,'category_id');
    }

    public function getCareerById(int $id)
    {
        $career =  $this->where('id', '=', $id)->first();
        $careerTranslation = $career->translations()->get();
        $career->translations = $careerTranslation;

        return $career;
    }
}
