<?php
/**
 * Dwaa Test Lunar - ${FILE_NAME}
 *
 * Date: 24/06/24
 * Time: 7:46 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Resources;

class TermsAndConditionsCollection extends MainCollection
{

    public function __construct($resource)
    {
        parent::__construct($resource, 'terms');
    }
}
