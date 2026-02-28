<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Accordion extends Model
{
    use HasFactory, SoftDeletes,Translatable;
    
    protected $table = 'accordions';

    protected $fillable = ['description','title','product_id',];
    public $translatedAttributes = ['description','title'];
    public function category()
    {
        return $this->belongsTo(Accordion::class);
    }

    public function getAccordionById(int $id)
    {
        $accordion =  $this->where('id', '=', $id)->first();
        if ($accordion) {
            $accordionTranslation = $accordion->translations()->get();
            $accordion->translations = $accordionTranslation;
        }

        return $accordion;
    }
}
