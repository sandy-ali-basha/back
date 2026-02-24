<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/13
 * Time: 6:37 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\Currency;

class DiscountResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'handle' => $this->handle,
            'coupon' => $this->coupon,
            'type' => $this->type,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'uses' => $this->uses,
            'max_uses' => $this->max_uses,
            'data' => $this->data,
        ];
    }
}
