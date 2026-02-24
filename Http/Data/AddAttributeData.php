<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/27
 * Time: 6:54 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class AddAttributeData extends Data
{
    public int   $product_id;
    public int   $attribute_id;
    public array $values;

    public function __construct(
        int   $product_id,
        int   $attribute_id,
        array $values,

    )
    {
        $this->attribute_id = $attribute_id;
        $this->product_id   = $product_id;
        $this->values       = $values;
    }
}
