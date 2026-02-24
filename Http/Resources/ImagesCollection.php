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

class ImagesCollection extends MainCollection
{

    public function __construct($resource)
    {
        parent::__construct($resource, 'images');
    }
}
