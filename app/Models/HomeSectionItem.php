<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSectionItem extends Model
{
    use HasFactory;

    protected $fillable = ['home_section_id', 'image', 'cta_link', 'title_en', 'description_en', 'title_ar', 'description_ar' ,'title_kr', 'description_kr'];

    public function section()
    {
        return $this->belongsTo(HomeSection::class);
    }
    public function getImageUrlAttribute()
    {
        return Storage::disk('home_storage')->url($this->image);
    }

}