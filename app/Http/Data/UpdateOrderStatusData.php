<?php


namespace App\Http\Data;

use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class UpdateOrderStatusData extends Data
{

    
    
    
    
    
    public function __construct(
        public int $id,
        public string $status
    )
    {

    }


    public static function rules(): array
    {
        return [
            "id" => ['required', 'integer', 'exists:lunar_orders,id'],
            "status" => ['required', 'string', Rule::in(array_keys(config('lunar.orders.statuses')))]
        ];
    }
}
