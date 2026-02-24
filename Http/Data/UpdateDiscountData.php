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

class UpdateDiscountData extends Data
{
    
    public function __construct(
        public string   $name,
        public string $type = 'Lunar\DiscountTypes\AmountOff',
        public string   $starts_at,
        public string $ends_at,
        public null|int $max_uses,
        public $data,
    )
    {

    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255|',
            'starts_at' => 'required|date_format:Y-m-d',
            'ends_at' => 'nullable|date_format:Y-m-d|after:starts_at',
            'max_uses' => ['nullable', 'int', 'min:1'],
            'data' => 'array',
            'data.percentage' => 'required_if:data.fixed_value,false|nullable|numeric|min:1',
            'data.fixed_values' => 'array|min:0',
            'data.fixed_values.*' => 'required_if:data.fixed_value,true|nullable|numeric|min:1',
            'data.fixed_value' => 'nullable|boolean',

        ];
    }
}
