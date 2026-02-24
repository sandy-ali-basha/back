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

class UpdateSettingsData extends Data
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
            'data.*.name' => Rule::forEach(function ($value, $attribute, $data) {
                $idKey = "data.$attribute.name";
                
                return [
                    'required', 'string', 'max:255', Rule::unique('settings')->ignore($data[$idKey])
                ];
            }),
            'data.*.value' => 'nullable|string',
            'data.*.options' => 'nullable|array',
        ];
    }
}
