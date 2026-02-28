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



class AddCustomerDataSocial extends Data
{
    public string $phone_number;
    public string $first_name;
    public string $last_name;
    public int $age;
    public string $gender;
    public int $user_id;

    public function __construct(
        int $user_id,
    )
    {
        $this->user_id = $user_id;
    }
    public static function rules(): array
    {
        return [
            'phone_number' => 'nullable|string|max:255||unique:customers,phone_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'age' => 'nullable|integer|max:255',
            'gender' => 'nullable|string|max:255',
        ];
    }
}
