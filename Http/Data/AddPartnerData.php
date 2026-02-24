<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Max;
class AddPartnerData extends Data
{
    public function __construct(
        #[Image, Mimes('jpg,jpeg,png'), Max(2048)]
        public ?UploadedFile $logo_url,
        public ?string $name
    ) {}


    public static function rules(): array
    {
        return [
            
        ];
    }
}
