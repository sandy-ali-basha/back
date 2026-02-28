<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddPostData extends Data
{

    public array $ar;
    public array $kr;
    public array $en;


    public function __construct(
        array  $ar,
        array  $kr,
        array  $en,

    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
    }


    public static function rules(): array
    {
        return [
            "ar.title" => "required",
            "en.title" => "required",
            "kr.title" => "required",
            "ar.text" => "required",
            "en.text" => "required",
            "kr.text" => "required",
        ];
    }
}
