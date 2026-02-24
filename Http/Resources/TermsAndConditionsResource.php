<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/07
 * Time: 5:17 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class TermsAndConditionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $term = $this->translate(App::getLocale()??"en");
        $data   = [
            'id'=>$this->id,
            'name'=>$term->name,
            'text'=>$term->text,


        ];
        if ($this->withTranslation) {
            $data['translations'] = $this->translations;
        }

        return $data;

    }
}
