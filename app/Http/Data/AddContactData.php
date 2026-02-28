<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddContactData extends Data
{
    public function __construct(
        public ?string $companyName,
        public ?string $facebook,
        public ?string $instagram,
        public ?string $linkedin,
        public ?string $whatsapp,
        public ?string $email,
    ) {}


    public static function rules(): array
    {
        return [
            
        ];
    }
}
