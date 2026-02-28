<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModelTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','locale','brand_id'];
    protected $table = 'brand_model_translation';
}
