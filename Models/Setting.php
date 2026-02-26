<?php

namespace App\Models;

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
        if (($this->options['type'] ?? '') === 'video') {
            $locale = app()->getLocale() ?? 'en';
            $allVideos = $this->getVideoByLocale();

            return $allVideos[$locale] ?? $allVideos['en'] ?? reset($allVideos) ?: null;
        }

        return json_decode($this->attributes['value'] ?? null, true);
    }

    public function getVideoAttribute()
    {
        return $this->getVideoByLocale();
    }

    private function getVideoByLocale(): array
    {
        $videos = ['ar' => null, 'en' => null, 'kr' => null];

        foreach ($this->getMedia('video') as $media) {
            $lang = $media->getCustomProperty('lang');
            if ($lang && array_key_exists($lang, $videos)) {
                $videos[$lang] = $media->getUrl();
            }
        }

        if (array_filter($videos)) {
            return $videos;
        }

        $firstVideo = $this->getFirstMediaUrl('video');
        if ($firstVideo) {
            $videos['en'] = $firstVideo;
        }

        return $videos;
    }

   

}
