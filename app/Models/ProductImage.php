<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\Product;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'image_path','type','image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
