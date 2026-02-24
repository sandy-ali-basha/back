<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionModelTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
    protected $table = 'product_option_model_translation';
}
