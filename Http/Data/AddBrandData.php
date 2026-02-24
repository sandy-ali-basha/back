<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 6:44 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AddBrandData extends Data
{
    public string   $name;
    public array    $en;
    public array    $ar;
    public array    $kr;

    public function __construct(
        string       $name,
        array $en,
        array $ar,
        array $kr,

    )
    {
        $this->en           = $en;
        $this->ar           = $ar;
        $this->kr           = $kr;
        $this->name           = $name;
    }

    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'en.name' => 'required',
            'ar.name' => 'required',
            'kr.name' => 'required',
        ];
    }
}
