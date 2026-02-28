<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSectionItem extends Model
{
    use HasFactory;

    protected $fillable = ['home_section_id', 'image', 'video_en', 'video_ar', 'video_kr', 'cta_link', 'title_en', 'description_en', 'title_ar', 'description_ar' ,'title_kr', 'description_kr'];

    public function section()
    {
        return $this->belongsTo(HomeSection::class);
    }
    public function getImageUrlAttribute()
    {
        return Storage::disk('home_storage')->url($this->image);
    }

    public function getVideoEnUrlAttribute()
    {
        return $this->video_en ? Storage::disk('home_storage')->url($this->video_en) : null;
    }

    public function getVideoArUrlAttribute()
    {
        return $this->video_ar ? Storage::disk('home_storage')->url($this->video_ar) : null;
    }

    public function getVideoKrUrlAttribute()
    {
        return $this->video_kr ? Storage::disk('home_storage')->url($this->video_kr) : null;
    }

}
