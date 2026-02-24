<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductModelTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['attribute_data'];
    protected $table = 'product_model_translation';
}
