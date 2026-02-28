<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/07
 * Time: 5:25 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateTermsData extends Data
{
    public array|null $kr;
    public array|null $en;
    public array|null $ar;


    public function __construct(
        array|null $kr,
        array|null $en,
        array|null $ar


    )
    {
        $this->ar = $ar;
        $this->kr = $kr;
        $this->en = $en;

    }
}
