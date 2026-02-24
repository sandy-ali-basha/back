<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 5:59 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateCustomerData extends Data
{
    public string $phone_number;
    public string $first_name;
    public string $last_name;
    public int $age;
    public string $gender;
    public int $user_id;

    public function __construct(

        string $phone_number,
        int $age,
        string $gender,
        int $user_id,
    )
    {
        $this->age         = $age;
        $this->gender      = $gender;
        $this->phone_number = $phone_number;
        $this->user_id = $user_id;
    }
    public static function rules(): array
    {
        return [
            'phone_number' => 'string|max:255||unique:customers,phone_number',
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'age' => 'integer|max:255',
            'gender' => 'string|max:255',
        ];
    }
}
