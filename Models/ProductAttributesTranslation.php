<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributesTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title'];
    protected $table = 'product_attributes_translation';
}
