<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddLocationData extends Data
{
    public ?string $map_url;
    public ?bool $is_main;

    public array $ar;
    public array $en;
    public array $kr;

    public function __construct(
        ?string $map_url,
        ?bool $is_main,
        array $ar,
        array $en,
        array $kr,
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
            'map_url' => 'nullable|string',
            'is_main' => 'nullable|boolean',

            'ar' => 'required|array',
            'ar.office_name' => 'required|string',
            'ar.address' => 'required|string',

            'en' => 'required|array',
            'en.office_name' => 'required|string',
            'en.address' => 'required|string',

            'kr' => 'required|array',
            'kr.office_name' => 'required|string',
            'kr.address' => 'required|string',
        ];
    }
}
