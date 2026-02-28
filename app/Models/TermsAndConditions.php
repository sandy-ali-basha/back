<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditions extends Model
{
    use HasFactory,Translatable;

    protected $fillable = [
        'text','name'
    ];
    protected $table = 'terms_and_conditions';
    public $translatedAttributes = ['text','name'];
    public function getTermById($id)
    {
        $term =  $this::find($id);
        $termTranslation = $term->translations()->get();
        $term->translations = $termTranslation;

        return $term;
    }
}
