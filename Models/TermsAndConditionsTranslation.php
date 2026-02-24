<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsAndConditionsTranslation extends Model
{
    public $timestamps = false;
    protected $table = 'terms_and_conditions_translation';
    protected $fillable = ['text','name'];

}
