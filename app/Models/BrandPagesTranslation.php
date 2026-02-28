<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandPagesTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','text','locale','brand_page_id'];
    protected $table = 'brand_page_translation';
}
