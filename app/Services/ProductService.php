<?php

namespace App\Services;

use App\Http\Data\AddAttributeData;
use App\Http\Data\AddDiscountData;
use App\Http\Patterns\AddProduct;
use App\Http\Patterns\DeleteProduct;
use App\Http\Patterns\UpdateProduct;
use App\Models\City;
use App\Models\ProductAttributes;
use App\Models\ProductAttributesValues;
use App\Models\ProductModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Currency;
use Lunar\Models\Discount;
use Lunar\Models\DiscountPurchasable;
use Lunar\Models\Price;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;


class ProductService
{

    protected ProductModel            $model;
    protected ProductAttributes       $attributes;
    protected ProductAttributesValues $values;

    public function __construct(ProductModel            $model,
                                ProductAttributes       $attributes,
                                ProductAttributesValues $values
    )
    {
        $this->model      = $model;
        $this->attributes = $attributes;
        $this->values     = $values;
    }

    public function getAllProducts(): Collection
    {
        return $this->model::query()
                           ->orderBy('created_at', 'desc')
                           ->get();

    }
    public function addVariantToProduct($id,$data)
    {
        $product = ProductModel::find($id);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $data['sku'],
            'unit_quantity' => $data['unit_quantity'],
            'tax_class_id' => $data['tax_class_id'],
            'stock' => $data['inventory'],
            'storage_qty' => $data['storage_qty'],
            'purchasable' => $data['purchasable'],
            'city_id' => $data['city_id'],
            'reorder_point' => $data['reorder_point'],
        ]);
        $flavor  = ProductOptionValue::where('product_option_id', 10)
                                     ->where('id', $data['option_value_ids'][0])
                                     ->first();
        $packing = ProductOptionValue::where('product_option_id', 11)
                                     ->where('id', $data['option_value_ids'][1])
                                     ->first();

        $variant->values()->attach([$flavor->id, $packing->id]);
        $variant->prices()->create([
            'price' => $data['price'],
            'currency_id' => City::find($data['city_id'])->currency_id,
            'compare_price' => $data['compare_price']??null,
            'compare_price_start_date' => $data['compare_price_start_date']??null,
            'compare_price_end_date' => $data['compare_price_end_date']??null,
        ]);
        return $product;
    }

    public function createProduct($data)
    {
        $brand = new AddProduct();

        return $brand->doOperation($data->toArray());
    }

    public function getById($id)
    {
        $product = $this->model->where("id", $id)->first();

        $translations = [];
        foreach (Config::get('translatable.locales') as $locale) {
            $translations[]        = [
                "locale" => $locale,
                "name" => $product->translateAttribute('name', $locale),
                "description" => $product->translateAttribute('description', $locale),
                "length" => $product->translateAttribute('length', $locale),
                "width" => $product->translateAttribute('width', $locale),
                "height" => $product->translateAttribute('height', $locale),
                "division" => $product->translateAttribute('division', $locale),
                "tag" => $product->translateAttribute('tag', $locale),

            ];
            $product->translations = $translations;
        }

        return $product;
    }

    public function getForCurrentCity($product, $cityId)
    {

        $city_id      = $cityId;
        $name         = $product->translateAttribute('name', 'en');
        $product      = $this->model->where('attribute_data->name->value->en', '=', $name)
                                    ->whereHas('cities', function($query) use ($city_id) {
                                        $query->where('world_city_id', $city_id);
                                    })->first();
        $translations = [];
        if ($product) {
            foreach (Config::get('translatable.locales') as $locale) {
                $translations[]        = [
                    "locale" => $locale,
                    "name" => $product->translateAttribute('name', $locale),
                    "description" => $product->translateAttribute('description', $locale),
                    "tag" => $product->translateAttribute('tag', $locale),

                ];
                $product->translations = $translations;
            }
        }

        return $product;
    }

    public function updateProduct($id, $data)
    {
        $brand = new UpdateProduct();

        return $brand->doOperation(['id' => $id, 'data' => $data->toArray()]);
    }

    public function deleteProduct($id)
    {
        $brand = new DeleteProduct();

        return $brand->doOperation(['id' => $id]);
    }

    public function getAttributeById($id)
    {
        return $this->attributes->where("id", $id)->first();
    }

    public function addAttributes($id, AddAttributeData $data)
    {
        $product = ProductModel::find($id);
        $product->ProductAttributesValues()->sync($data->values);
        $mainProduct = ProductModel::find($id);
        foreach (Config::get('translatable.locales') as $locale) {
            $translations[]            = [
                "locale" => $locale,
                "name" => $mainProduct->translateAttribute('name', $locale),
                "description" => $mainProduct->translateAttribute('description', $locale),
                "description" => $mainProduct->translateAttribute('tag', $locale)
            ];
            $mainProduct->translations = $translations;
        }

        return $mainProduct;
    }
    public function filterData(?array $filters, ?int $brandId, ?int $productTypeId, ?float $minPrice, ?float $maxPrice)
    {
        return ProductModel::filterByAttributes($filters, $brandId, $productTypeId, $minPrice, $maxPrice);
    }
    public function addQtyAndPrice($id, $options)
    {
        $product = ProductModel::find($id);



if($options!=null){
    foreach ($options as $data) {
        // 1. Create the variant
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $data['sku'],
            'unit_quantity' => $data['unit'],
            'tax_class_id' => $data['tax_class_id'],
            'stock' => $data['inventory'],
            'storage_qty' => $data['qty'],
            'purchasable' => $data['purchasable'],
            'city_id' => $data['city_id'],
            'reorder_point' => $data['reorder_point'],
        ]);
        $flavor  = ProductOptionValue::where('product_option_id', 10)
                                     ->where('id', $data['option_value_ids'][0])
                                     ->first();
        $packing = ProductOptionValue::where('product_option_id', 11)
                                     ->where('id', $data['option_value_ids'][1])
                                     ->first();

        $variant->values()->attach([$flavor->id, $packing->id]);
        $variant->prices()->create([
            'price' => $data['price'],
            'currency_id' => City::find($data['city_id'])->currency_id,
            'compare_price' => $data['compare_price']??null,
            'compare_price_start_date' => $data['compare_price_start_date']??null,
            'compare_price_end_date' => $data['compare_price_end_date']??null,
        ]);
    }
}

        return $variant;
    }


    public function updateQtyAndPrice($id,
                                      ?float $price,
                                      ?int $qty,
                                      ?string $purchasable,
                                      ?float $compare_price,
        $sku = null)
    {
        $currency = Currency::first();
        $product  = ProductModel::find($id);
        $variant  = ProductVariant::where('product_id', $id)->first();
        $quantity = $qty ?? $variant->storage_qty;
        $variant->update([
            'storage_qty' => $quantity ?? $variant->storage_qty,
            "purchasable" => $purchasable ?? "always",
            'sku' => $sku ?? $variant->sku,
        ]);
        $priceModel = Price::where('priceable_type', 'Lunar\Models\ProductVariant')->where('priceable_id', $variant->id)->first();
        $price      = $price ?? $priceModel->price;
        $priceModel->update([
            'price' => $price,
            'currency_id' => $currency->id,
            'compare_price' => $compare_price
        ]);
        $product->sku           = $variant->sku;
        $product->unit_quantity = $variant->unit_quantity;
        $product->price         = $variant->getPrices()->first();

        return $variant;
    }

    public function getSimilarProducts(int $id)
    {
        // Retrieve the product
        $product = ProductModel::find($id);

        // Retrieve the attribute value for attribute_id 1
        $attributeValue = $product->ProductAttributesValues()->where('product_attributes_id', 6)->first();
        // Initialize an empty collection for similar products
        $similarProducts = collect();
        if ($attributeValue) {
            // Find all products with the same attribute value
            $similarProducts = ProductModel::whereHas('ProductAttributesValues', function($query) use ($attributeValue
            ) {
                $query
                    ->where('product_attributes_values_id', $attributeValue->id);
            })->where('id', '!=', $product->id)->pluck('id');
        }
        $city_id = session()->get('city_id', 1);

        return ProductModel::whereIn('id', $similarProducts)->whereHas('cities', function($query) use ($city_id) {
            $query->where('world_city_id', $city_id);
        })->where('status', 'active')->get();
    }

    public function addDiscount(int $id, AddDiscountData $data)
    {
        $discount = Discount::create([
            'name' => $data->name,
            'handle' => $data->name,
            'type' => 'Lunar\DiscountTypes\AmountOff',
            'data' => [
                'fixed_value' => $data->value,
                'min_prices' => [
                    Currency::first()->name => $data->min_price
                ],
            ],
            'starts_at' => $data->starts_at,
            'ends_at' => $data->ends_at,
            'max_uses' => $data->max_uses,
        ]);
        DiscountPurchasable::create([
            "discount_id" => $discount->id
        ]);

    }


    public function getProudctsWithOffers()
    {
        $city_id = session()->get('city_id', 1);

        return $this->model::whereHas('variants', function($query) {
            $query->where('storage_qty', '>', 0)
                  ->whereHas('prices', function($q) {
                      $q->whereNotNull('compare_price')
                        ->where('compare_price', '>', 0);
                  });
        })->whereHas('cities', function($query) use ($city_id) {
            $query->where('world_city_id', $city_id);
        })->where('status', 'active')
                           ->get();
    }

    // public function getVariantById($id)
    // {
    //     return ProductVariant::where("product_id", $id)->get();

    // }

public function getVariantById($id)
{
    return ProductVariant::find($id); // returns a single model
}

    public function updateProuctVariant($id, ?array $options)
    {
        $variant = ProductVariant::where("id", $id)->first();

        try {

      $variant->update([
                'tax_class_id' => $options['tax_class_id']??$variant->tax_class_id,
                'sku' => $options['sku']??$variant->sku,
                'unit_quantity' => $options['unit_quantity']??$variant->unit_quantity,
                'purchasable' => $options['purchasable']??$variant->purchasable,
                'storage_qty' => $options['storage_qty']??$variant->storage_qty,
                'stock' => $options['stock']??$variant->stock,
                'city_id' => $options['city_id']??$variant->city_id,
                'reorder_point' => $options['reorder_point']??$variant->reorder_point,
            ]);
            $priceModel = Price::where('priceable_type', 'Lunar\Models\ProductVariant')->where('priceable_id', $variant->id)->first();
            $price      = $options['price'] ?? $priceModel->price;

            $currency = City::find($options['city_id'])->currency_id??City::find($variant->city_id)->currency_id;
            $priceModel->update([
                'price' => $price,
                'currency_id' => $currency,
                'compare_price' => $options['compare_price']??$variant->compare_price,
                'compare_price_start_date' => $options['compare_price_start_date'],
                'compare_price_end_date' => $options['compare_price_end_date']
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }


    public function getVariants($id)
    {
        return ProductVariant::where("product_id", $id)->get();
    }

}
