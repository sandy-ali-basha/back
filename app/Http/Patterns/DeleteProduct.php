<?php
namespace App\Http\Patterns;;

use App\Models\Accordion;
use App\Models\City;
use Lunar\Models\Product;
use App\Models\Logs;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteProduct implements IOperations
{
    public function doOperation(array $data)
    {
        $product = ProductModel::find($data['id']);
        if ($product->images()){
            $product->images()->delete();
        }

        Accordion::where('product_id',$product->id)->delete();

        if ($product->cities()) {
            DB::table('products_world_cities')->where('product_id', $product->id)->delete();        
        }
        if ($product->brand) {
            $product->brand()->dissociate();
        }
        return $product->delete();
    }

    public function audit()
    {
        Logs::createNewRecord('delete', 'Product');
    }

    public function getErrorMessage()
    {
        Log::info('ERROR IN ADD NEW Product us IN Product CONTROLLER');
    }

    public function returnPage()
    {
        return redirect()->back();
    }
}
