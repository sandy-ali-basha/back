<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['text','title'];
    protected $table = 'post_translation';
}
