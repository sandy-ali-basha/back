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


class AddPasswordData extends Data
{
    public string $email;
    public string $password;
    public string $password_confirmation;


    public function __construct(
        string $email,
        string $password,
        string $password_confirmation,


    )
    {

    }
    public static function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
