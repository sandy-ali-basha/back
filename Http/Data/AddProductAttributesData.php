<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Illuminate\Http\UploadedFile;
class AddProductAttributesData extends Data
{

    public array $ar;
    public array $kr;
    public array $en;
     public bool $status;
    public bool $navActive;
    public UploadedFile $image_url;

    public function __construct(
        array  $ar,
        array  $kr,
        array  $en,
        bool $status,
        bool $navActive,
        UploadedFile $image_url

    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
        $this->status = $status;
        $this->navActive = $navActive;
        $this->image_url = $image_url;
        
    }


    public static function rules(): array
    {
        return [
            "ar.title" => "required",
            "en.title" => "required",
            "kr.title" => "required",
            "status" => ['required', 'boolean'],
            "navActive" => ['required', 'boolean'],
            'image_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

        ];
    }
}
