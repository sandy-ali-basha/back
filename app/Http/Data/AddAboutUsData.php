<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Max;
class AddAboutUsData extends Data
{
    public string $section;
    public array $ar;
    public array $en;
    public array $kr;

    public function __construct(
        string $section,
        #[Image, Mimes('jpg,jpeg,png'), Max(2048)]
        public ?UploadedFile $image_url,
        array $ar,
        array $en,
        array $kr,
    ) {
        $this->section = $section;
        $this->image_url = $image_url;

        $this->ar = $ar;
        $this->en = $en;
        $this->kr = $kr;
    }

    public static function rules(): array
    {
        return [
            'section'   => 'required|in:mission,vision,culture,partners',
            'image_url' => 'required|file|Mimes:png,jpeg,jpg',

            'ar.title' => 'required|string',
            'ar.description' => 'required|string',

            'en.title' => 'required|string',
            'en.description' => 'required|string',

            'kr.title' => 'required|string',
            'kr.description' => 'required|string',
        ];
    }
}
