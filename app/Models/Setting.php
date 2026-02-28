<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Casts\Attribute as CastsAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Base\BaseModel;
use Lunar\Base\Traits\HasMedia;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;


class Setting extends BaseModel implements SpatieHasMedia
{
    use HasFactory, HasMedia;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable('settings');
        if ($connection = config('lunar.database.connection', false)) {
            $this->setConnection($connection);
        }
    }
    protected $appends = ['video']; // يضيف الـ attribute تلقائيًا في toArray و toJson
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = [
        'active',
        'order',
        'name',
        'value',
        'options'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    // protected function value(): CastsAttribute {
    //     return CastsAttribute::make(
    //         get: fn(string $val) => isset($this->options['type']) && ($this->options['type'] === 'slide' || $this->options['type'] === 'json') ? json_decode($val, true) : $val,
    //         //set: fn(array $val) => isset($this->options['type']) && $this->options['type'] === 'slide' ? json_encode($val) : $val
    //     );
    // }
    protected function value(): CastsAttribute {
    return CastsAttribute::make(
        get: fn($val) =>
            isset($this->options['type'])
            && in_array($this->options['type'], ['slide', 'json'])
            && is_string($val)
                ? json_decode($val, true)
                : $val,
    );
}
  public function getValueAttribute()
    {
        // إذا النوع video
        if (($this->options['type'] ?? '') === 'video') {
            // جلب رابط الفيديو المرتبط بالـ Setting الحالي فقط
            return $this->getFirstMediaUrl('video');
        }
    
        // إذا مش فيديو، نرجع القيمة الأصلية (مثلاً JSON)
        return json_decode($this->attributes['value'] ?? null, true);
    }
    public function getVideoAttribute(){
        
    }
   

}
