<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Lunar\Base\Traits\LunarUser;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use  LunarUser,HasApiTokens,HasRoles, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider_name',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
    ];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function setProviderTokenAttribute($value){
        return $this->attributes['provider_token'] = Crypt::crypt($value);
    }

    public function getProviderTokenAttribute($value)
    {
        return Crypt::decrypt($value);
    }

    public function rewardPoints() {
        return $this->hasOne(RewardPoint::class, 'user_id', 'id');
    }

    public function increasePoints($points) {
        if ($this->rewardPoints()->count() > 0) {
            $this->rewardPoints()->first()->increment('points', $points);
        } else {
            $this->rewardPoints()->create(['points' => $points, 'user_id' => $this->id]);
        }
    }

    public function decreasePoints($points) {
        $this->rewardPoints()->decrement('points', $points);
    }

    public function customer()
    {
        return $this->hasMany(Customer::class, 'user_id', 'id');
    }

}
