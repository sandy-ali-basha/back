<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CareerTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['vacancy_name', 'location','country','description','about_us'];
}
