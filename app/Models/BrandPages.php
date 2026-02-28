<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandPages extends Model
{
    use HasFactory,Translatable;
    public $translatedAttributes = ['name', 'text'];
    protected $fillable = ['name','brand_id','text'];
    protected $table ="brand_pages";

    public function slides()
    {
        return $this->hasMany(BrandSlide::class,'brand_page_id');
    }
      public function brand()
    {
        return $this->belongsTo(BrandModel::class,'brand_id');
    }
}
