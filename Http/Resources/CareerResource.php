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
use Illuminate\Support\Facades\App;

class CareerResource extends JsonResource
{
    public function toArray(Request $request)
    {
        $career = $this->translate(App::getLocale()??"en");
        $data   = [
            'id' => $this->id,
            'vacancy_name' => $career->vacancy_name,
            'requisition_no' => $this->requisition_no,
            'time_type' => $this->time_type,
            'location' => $career->location,
            'country' => $career->country,
            'description' => $career->description,
            'about_us' => $career->about_us,
            'category' => $this->category->name,


        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;
    }
}
