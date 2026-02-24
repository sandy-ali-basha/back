<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Post extends Model
{
    use HasFactory, SoftDeletes,Translatable;

    protected $fillable = ['text','title'];
    public $translatedAttributes = ['text','title'];
    public function category()
    {
        return $this->belongsTo(Post::class);
    }

    public function getPostById(int $id)
    {
        $post =  $this->where('id', '=', $id)->first();
        $postTranslation = $post->translations()->get();
        $post->translations = $postTranslation;

        return $post;
    }
}
