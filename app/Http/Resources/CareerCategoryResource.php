<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 5:03 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CareerCategoryResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $careerCategory = $this->translate("en");
        $data   = [
            'id' => $this->id,
            'name' => $this->name,
            'nav_active'=>$this->nav_active,
            'home_active'=>$this->home_active
        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }
        return $data;
    }
}
