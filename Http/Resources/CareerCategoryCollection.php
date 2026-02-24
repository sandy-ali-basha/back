<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 6:50 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Resources;

class CareerCategoryCollection extends MainCollection
{

    public function __construct($resource)
    {
        parent::__construct($resource, 'careers_categories');
    }
}
