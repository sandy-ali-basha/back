<?php

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateSlideData extends Data
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
            'slides' => 'required|array',
            'slides.*.name' => 'nullabl|string|max:255',
            'slides.*.ar.title' => 'nullabl|string|max:255',
            'slides.*.kr.title' => 'nullabl|string|max:255',
            'slides.*.en.title' => 'nullabl|string|max:255',
            'slides.*.ar.text' => 'nullabl|string',
            'slides.*.kr.text' => 'nullabl|string',
            'slides.*.en.text' => 'nullabl|string',
            'slides.*.link' => 'nullabl|url',
            'slides.*.image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ];
    }
}
