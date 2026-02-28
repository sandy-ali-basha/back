<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddSlideData extends Data
{
    public array $slides;


    public function __construct(
        array $slides,
    )
    {
        $this->slides       = $slides;
    }

    public static function rules(): array
    {
        return [
            'slides' => 'nullable|array',
            'slides.*.ar.title' => 'nullable|string|max:255',
            'slides.*.kr.title' => 'nullable|string|max:255',
            'slides.*.en.title' => 'nullable|string|max:255',
            'slides.*.ar.text' => 'nullable|string',
            'slides.*.kr.text' => 'nullable|string',
            'slides.*.en.text' => 'nullable|string',
            'slides.*.link' => 'nullable|url',
            'slides.*.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ];
    }
}
