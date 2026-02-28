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


use Spatie\LaravelData\Data;


class UpdateUserData extends Data
{
    public string $email;

    public function __construct(
        string $email,

    )
    {

    }
    public static function rules(): array
    {
        return [

        ];
    }
}
