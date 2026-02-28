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

use App\Http\Helpers\CodeGenerator;
use Spatie\LaravelData\Data;

class AddDiscountData extends Data
{
    public string       $name;
    public string       $starts_at;
    public string       $ends_at;
    public null|int     $max_uses;
    public              $data;
    public ?string       $type;
    public ?string       $handle;
    public ?string       $coupon;
    public ?int       $uses;
    public function __construct(
        string       $name,
        string       $starts_at,
        string       $ends_at,
        null|int     $max_uses,
        $data,
    )
    {
        $this->name = $name;
        $this->starts_at = $starts_at;
        $this->ends_at = $ends_at;
        $this->max_uses = $max_uses;
        $this->data = $data;
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
