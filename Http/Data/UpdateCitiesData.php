<?php
/**
 * Dawaa - RegisterData.php
 *
 * Date: 24/04/28
 * Time: 7:58 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Illuminate\Support\Str;

class UpdateCitiesData extends Data
{
    public array $data;

    public function __construct(
        array $data,
    )
    {

    }
    public static function rules(): array
    {
        return [
            'data' => 'required|array',
            'data.*.id' => "required|exists:world_states,id",
            'data.*.name' => "required|string",
            'data.*.shipping_price' => 'required|numeric',
            'data.*.currency_id' => 'required|numeric',
        ];
    }
}
