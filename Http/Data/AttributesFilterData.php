<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/27
 * Time: 7:20 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AttributesFilterData extends Data
{
    public array|null $filters;
    public int|null $product_type_id;
    public int|null $brand_id;
    public float|null $max_price;
    public float|null $min_price;

    public function __construct(
        array $filters,
        int|null $product_type_id,
        int|null $brand_id,
        float|null $min_price,
        float|null $max_price

    )
    {
        $this->filters       = $filters;
        $this->product_type_id       = $product_type_id;
        $this->brand_id       = $brand_id;
        $this->min_price       = $min_price;
        $this->max_price       = $max_price;
    }
}
