<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lunar\Base\Traits\HasMedia;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;
use Lunar\Base\BaseModel;

class Partner  extends BaseModel implements SpatieHasMedia
{
     public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('partners');

        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
    use HasFactory, SoftDeletes , HasMedia;
    protected $table ="partners";
    protected $fillable = ["name"];
    protected $hidden = ["created_at","updated_at"];
    
    public function getLogoUrlAttribute()
    {
        return $this->getFirstMediaUrl('partners') 
            ?: asset('images/default-partner.png');
    }

    
}
