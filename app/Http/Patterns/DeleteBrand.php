<?php
/**
 * dawaa - ${FILE_NAME}
 *
 * Date: 24/05/06
 * Time: 5:56 PM
 * @author    Feras Alshaher <feras@lamsaworld.com>
 * @copyright Copyright (c) 2024 LamsaWorld (http://www.lamsaworld.com/)
 */

namespace App\Http\Patterns;


use App\Models\BrandModel;
use App\Models\Logs;
use Illuminate\Support\Facades\DB;
use Lunar\Models\Brand;

class DeleteBrand implements IOperations
{
    public function doOperation(array $data)
    {
        $brand = BrandModel::find($data['id']);
        $brand->deleteTranslations();
        if($brand->translations) {
            $brand->translations()->delete();
        }
        DB::table('lunar_products')->where('brand_id', $brand->id)->update(['brand_id' => null]);
        return $brand->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('Delete', 'Brand');
    }

    public function getErrorMessage()
    {
        \Illuminate\Support\Facades\Log::info('ERROR IN ADD Delete brand IN brand CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
