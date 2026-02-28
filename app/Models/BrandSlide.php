<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandSlide extends Model
{
    use HasFactory,Translatable;
    public $translatedAttributes = ['name', 'title'];
    protected $fillable = ['brand_page_id', 'title', 'text', 'image_path','image'];
    protected $table ="brand_slides";

    public function brandPage()
    {
        return $this->belongsTo(BrandPages::class,'brand_page_id');
    }
}
