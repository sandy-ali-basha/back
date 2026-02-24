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


class AddUserDataSocial extends Data
{
    public string $name;
    public string $email;

    public function __construct(
        string $name,
        string $email,

    )
    {

    }
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
        ];
    }
}
