<?php


namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateAddressData extends Data
{
    public bool|null $billing_default;
    public int    $user_id;
    public bool|null   $shipping_default;
    public string $title;
    public string $first_name;
    public string|null $contact_email;
    public string|null $contact_phone;
    public string $last_name;
    public string $city;
    public string $line_one;
    public string|null $state;
    public string|null $postcode;
    public string|null $delivery_instructions;
    public int    $country_id;


    public function __construct(
        bool|null $billing_default,
        int    $user_id,
        bool|null   $shipping_default,
        string $title,
        string $first_name,
        string $last_name,
        string|null $contact_email,
        string|null $contact_phone,
        string $city,
        string $line_one,
        string|null $state,
        string|null $postcode,
        int    $country_id,
        string|null $delivery_instructions

    )
    {
        $this->billing_default  = $billing_default;
        $this->user_id      = $user_id;
        $this->shipping_default = $shipping_default;
        $this->title            = $title;
        $this->first_name       = $first_name;
        $this->last_name        = $last_name;
        $this->contact_email        = $contact_email;
        $this->contact_phone        = $contact_phone;
        $this->city             = $city;
        $this->state            = $state;
        $this->line_one            = $line_one;
        $this->postcode         = $postcode;
        $this->country_id       = $country_id;
        $this->delivery_instructions       = $delivery_instructions;

    }


    public static function rules(): array
    {
        return [

        ];
    }
}
