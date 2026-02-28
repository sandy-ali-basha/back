<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:09 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright XYZ
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateCareerCategoryData extends Data
{
    public array|NULL    $ar;
    public array|NULL    $kr;
    public array|NULL    $en;

    public function __construct(
                                array|NULL $ar,
                                array|NULL $kr,
                                array|NULL $en)
    {

        $this->ar           = $ar;
        $this->kr           = $kr;
        $this->en           = $en;
    }

}
