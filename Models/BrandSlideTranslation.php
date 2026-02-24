<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandSlideTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title','text','locale','brand_slide_id'];
    protected $table = 'brand_slide_translation';
}
