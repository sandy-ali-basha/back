<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/30
 * Time: 8:57 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UsePointsData extends Data
{

    
    public int         $points_to_use;

    public function __construct(
        int      $points_to_use,
    )
    {
        $this->points_to_use      = $points_to_use;
        
    }

    public static function rules(): array
    {
        return [
            'cart_id' => 'required|exists:lunar_carts,id',
            'points_to_use' => 'required|int|min:1|',
        ];
    }
}
