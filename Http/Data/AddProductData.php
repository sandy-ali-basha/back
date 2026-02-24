<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddProductData extends Data
{
    public int $brand_id;
    public int $product_type_id;
    public string $sku;
    public string $status;
    public string $description;
    public array $ar;
    public array $kr;
    public array $en;
    public array $options;
    public null|int $length;
    public null|int $density;
    public null|int $width;
    public null|int $height;
    public null|int $division;
    public null|int $weight;
    public null|int $points;
    public function __construct(
        int $brand_id,
        int $product_type_id,
        string $sku,
        string $status,
        array $options,
        null|int $length,
        null|int $density,
        null|int $width,
        null|int $height,
        null|int $division,
        null|int $weight,
        string $description,
        array $ar,
        array $kr,
        array $en,
        null|int $points

    ) {
        $this->brand_id = $brand_id;
        $this->product_type_id = $product_type_id;
        $this->sku = $sku;
        $this->status = $status;
        $this->description = $description;
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;
        $this->density = $density;
        $this->height = $height;
        $this->weight =$weight;
        $this->division = $division;
        $this->length = $length;

        $this->points = $points;
    }
    public static function rules(): array
    {
        return [
            "brand_id" => "required",
            "product_type_id" => "required",
            "options.*.city_id" => "required",
            "sku" => "required",
            "status" => "required",
            "ar.name" => "required",
            "en.name" => "required",
            "kr.name" => "required",
             // **Validation rules Tags **
            "ar.tag" => "required_with:en.tag,kr.tag",
            "en.tag" => "required_with:ar.tag,kr.tag",
            "kr.tag" => "required_with:ar.tag,en.tag",
            
            "description" => "required",
            'options.*.compare_price_start_date' => 'nullable|date',
            'options.*.compare_price_end_date'   => 'nullable|date|after_or_equal:options.*.compare_price_start_date',
        ];
    }
}
