<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTypeModelTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','locale','product_type_model_id'];
    protected $table = 'product_type_model_translation';
}
