<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lunar\Models\Customer as ModelsCustomer;

class Customer extends ModelsCustomer
{
    // use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'gender',
        'phone_number',
        'is_verified',
        'user_id',
    ];
    // public function users()
    // {
    //     return $this->belongsTo(User::class);
    // }
    
    public function orders()
{
    return $this->hasMany(\Lunar\Models\Order::class);
}
}
