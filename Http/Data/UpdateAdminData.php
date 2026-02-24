<?php
/**
 * Dawaa - UpdateAdminData.php
 *
 * Date: 24/04/28
 * Time: 7:58 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use App\Http\Helpers\Constants;
use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateAdminData extends Data
{
    public ?string $name;
    public ?string $email;
    public ?string $password;
    public ?string $role;

    public function __construct(
        ?string $name = null,
        ?string $email = null,
        ?string $password = null,
        ?string $role = null
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    public static function rules(): array
    {
        $user = User::find(request()->get('id'));

        return [
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(optional($user)->id),
            ],
            'password' => 'nullable|string|min:6',
            'role' => [
                'nullable', // If you want this optional too
                'string',
                Rule::in([
                    Constants::ECOMMERCE_ADMIN,
                    Constants::ROLE_SUPER_ADMIN,
                    Constants::WEBSITE_ADMIN,
                ]),
            ],
        ];
    }
}
