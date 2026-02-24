<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/30
 * Time: 5:34 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class PriceData extends Data
{

    public int $qty;
    public int $price;


    public function __construct(
        int $qty,
        int $price,


    )
    {
        $this->qty   = $qty;
        $this->price = $price;

    }


    public static function rules(): array
    {
        return [

        ];
    }
}
