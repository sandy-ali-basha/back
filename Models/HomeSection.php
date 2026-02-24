<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeSection extends Model
{
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at'];
 

    protected $fillable = ['type', 'title_en', 'description_en', 'title_ar', 'description_ar' ,'title_kr', 'description_kr','active',
        'order'];

    public function items()
    {
        return $this->hasMany(HomeSectionItem::class);
    }
}