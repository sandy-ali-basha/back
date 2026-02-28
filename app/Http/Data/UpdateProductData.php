<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateProductData extends Data
{
    public int|null    $brand_id;
    public int|null    $product_type_id;
    public string|null $sku;
    public string|null $description;
    public array|null  $ar;
    public array|null  $kr;
    public array|null  $en;
    public null|int $length;
    public null|int $density;
    public null|int $width;
    public null|int $height;
    public null|int $division;
    public null|int $weight;
    public null|int $points;
// New field for region-specific prices

    public function __construct(
        int|null    $brand_id,
        int|null    $product_type_id,
        string|null $sku,
        string|null $description,
        array|null  $ar,
        array|null  $kr,
        array|null  $en,
        null|int $length,
        null|int $density,
        null|int $width,
        null|int $height,
        null|int $division,
        null|int $weight,
        null|int $points

    )
    {
        $this->brand_id        = $brand_id;
        $this->product_type_id = $product_type_id;
        $this->sku             = $sku;
        // $this->status          = $status;
        $this->description     = $description;
        $this->ar              = $ar;
        $this->kr              = $kr;
        $this->en              = $en;
        $this->density = $density;
        $this->height = $height;
        $this->weight =$weight;
        $this->division = $division;
        $this->length = $length;
        $this->width = $width;
        $this->points = $points;
    }


    public static function rules(): array
    {
        return [

        ];
    }
}
