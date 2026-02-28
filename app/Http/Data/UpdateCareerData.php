<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 7:21 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Data;

use Spatie\LaravelData\Data;

class UpdateCareerData extends Data
{
    public string|null $requisition_no;
    public string|null $time_type   = null;
    public int|null    $category_id;
    public array|NULL    $ar;
    public array|NULL    $kr;
    public array|NULL    $en;

    /**1
     * @param string|NULL $requisition_no
     * @param string|NULL $time_type
     * @param int|NULL    $category_id
     * @param array|NULL  $ar
     * @param array|NULL  $kr
     * @param array|NULL  $en
     */
    public function __construct(string|null $requisition_no,
                                string|null $time_type,
                                int|null    $category_id,
                                array|NULL $ar,
                                array|NULL $kr,
                                array|NULL $en)
    {
        $this->requisition_no = $requisition_no;
        $this->time_type      = $time_type;
        $this->category_id    = $category_id;
        $this->ar           = $ar;
        $this->kr           = $kr;
        $this->en           = $en;
    }


    public static function rules(): array
    {
        return [
            'requisition_no' => 'string|max:255',
            'time_type' => 'max:255',
            'category_id' => 'exists:career_categories,id',
        ];
    }
}
