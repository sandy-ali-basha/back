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


class ChangePasswordData extends Data
{
    public string $current_password;
    public string $password;
    public string $password_confirmation;


    public function __construct(
        string $current_password,
        string $password,
        string $password_confirmation,


    )
    {

    }
    public static function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
