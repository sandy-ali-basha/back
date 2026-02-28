<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

use Illuminate\Http\UploadedFile; 

use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Max;
class UpdateAboutUsData extends Data
{
    public ?string $section = null;

    #[Image, Mimes('jpg,jpeg,png'), Max(2048)]
    public ?UploadedFile $image_url = null;

    public ?array $ar = null;
    public ?array $en = null;
    public ?array $kr = null;

    public static function rules(): array
    {
       return [];
    }
}

