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

use App\Http\Helpers\Constants;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;


class AddAdminData extends Data
{
    public string $name;
    public string $email;
    public string $password;
    public string $role;

    public function __construct(
        string $name,
        string $email,
        string $password,
        string $role,

    )
    {

    }
    public static function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required','string', Rule::in([
                Constants::ECOMMERCE_ADMIN,
                Constants::ROLE_SUPER_ADMIN,
                Constants::WEBSITE_ADMIN,
                Constants::ORDERS_ADMIN,
            ])]
        ];
    }
}