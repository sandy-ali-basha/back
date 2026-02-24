<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateLocationData extends Data
{
    public ?string $map_url;
    public ?bool $is_main;

    public ?array $ar;
    public ?array $en;
    public ?array $kr;

    public function __construct(
        ?string $map_url = null,
        ?bool $is_main = null,
        ?array $ar = null,
        ?array $en = null,
        ?array $kr = null,
    ) {
        $this->map_url = $map_url;
        $this->is_main = $is_main;

        $this->ar = $ar;
        $this->en = $en;
        $this->kr = $kr;
    }

    public static function rules(): array
    {
        return [
            'map_url' => 'sometimes|nullable|string',
            'is_main' => 'sometimes|nullable|boolean',

            'ar' => 'sometimes|array',
            'ar.office_name' => 'sometimes|required|string',
            'ar.address' => 'sometimes|required|string',

            'en' => 'sometimes|array',
            'en.office_name' => 'sometimes|required|string',
            'en.address' => 'sometimes|required|string',

            'kr' => 'sometimes|array',
            'kr.office_name' => 'sometimes|required|string',
            'kr.address' => 'sometimes|required|string',
        ];
    }
}
