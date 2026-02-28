<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdatePharmacyData extends Data
{
    public function __construct(
        public ?string $name,
        public ?float $lat,
        public ?float $lng,
        public ?string $city,
        public ?string $phone,
        public ?string $address,
    ) {}
}


