<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddBrandPageSlideData extends Data
{
    public int   $brand_id;
    public array $slides;


    public function __construct(
        array $slides,
        int   $brand_id

    )
    {

        $this->slides       = $slides;
        $this->brand_id = $brand_id;

    }

    public static function rules(): array
    {
        return [
            'slides' => 'required|array',
            'slides.*.ar.title' => 'required|string|max:255',
            'slides.*.kr.title' => 'required|string|max:255',
            'slides.*.en.title' => 'required|string|max:255',
            'slides.*.ar.text' => 'nullable|string',
            'slides.*.kr.text' => 'nullable|string',
            'slides.*.en.text' => 'nullable|string',
            'slides.*.image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ];
    }
}
