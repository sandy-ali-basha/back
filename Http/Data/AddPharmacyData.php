<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;

use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Max;
namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddPharmacyData extends Data
{
    public function __construct(
        public string $name,
        public float $lat,
        public float $lng,
        public string $city,
        public ?string $phone,
        public string $address,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
            'city' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'address' => ['required', 'string'],
        ];
    }
}

