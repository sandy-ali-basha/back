<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/07/03
 * Time: 8:34 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;

use App\Models\Logs;
use Illuminate\Support\Facades\Log;
use Lunar\Facades\DB;
use Lunar\Models\Discount;

class AddBrandDiscount
{
    public function doOperation(array $data)
    {
        try {
            $discount = Discount::create([
                'name' => $data['brandDiscountData']->name,
                'handle' => $data['brandDiscountData']->name . "_brand",
                'type' => "Lunar\DiscountTypes\AmountOff",
                'data' => [
                    'brand' => $data['brandDiscountData']->name,
                    'min_prices' => [
                        $data['brandDiscountData']->currency => $data['brandDiscountData']->min_price
                    ],
                ],
                'starts_at' => $data['brandDiscountData']->starts_at,
                'ends_at' => $data['brandDiscountData']->ends_at,
                'max_uses' => $data['brandDiscountData']->max_uses,
            ]);
            DB::table('lunar_brand_discount')->insert(['brand_id' => $data['brandId'], 'discount_id' => $discount->id, 'created_at' => $discount->created_id]);


            return true;
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
    public function audit()
    {
        Logs::createNewRecord('Add', 'Brand Discount');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Brand Discount IN brand CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
