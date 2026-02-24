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
use Illuminate\Validation\Rule;

class AddUserData extends Data
{
    public string $name;
    public string $email;
    public string $password;

    public function __construct(
        string $name,
        string $email,
        string $password,

    )
    {

    }
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
       'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
