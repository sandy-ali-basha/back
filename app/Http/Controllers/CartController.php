<?php

namespace App\Http\Controllers;

use App\Http\Data\AddCartData;
use App\Http\Data\UseDiscountData;
use App\Http\Data\UsePointsData;
use App\Http\Resources\CartCollection;
use App\Http\Resources\CartResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Services\CartService;
use App\Services\SettingService;
use App\Models\City;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lunar\Base\CartSessionInterface;
use Lunar\Facades\DB;
use Lunar\Models\Cart;
use Lunar\Models\CartLine;
use Lunar\Models\ProductVariant;
use Symfony\Component\HttpFoundation\Response;

class CartController extends Controller
{

    public CartService    $cartService;
    public SettingService $settingsService;
    protected             $cartSession;

    public function __construct(CartService          $cartService,
                                CartSessionInterface $cartSession,
                                SettingService       $settingsService)
    {
        $this->cartService     = $cartService;
        $this->cartSession     = $cartSession;
        $this->settingsService = $settingsService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carts        = $this->cartService->getAllCarts();
        $cartResource = new CartCollection($carts);
        $response     = new SuccessResponse($cartResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function useDiscount(Request $request)
    {

        $data = UseDiscountData::from($request);
        $cart = $this->cartService->getById($request->cart_id);
        if ($cart->points_used > 0) {
            $response = new ErrorResponse('You cant use points with coupon codes.', Response::HTTP_BAD_REQUEST);

            return response()->error($response);
        }


        $cart         = $this->cartService->addDiscount($data, $cart);
        $cartResource = new CartResource($cart);
        $response     = new SuccessResponse($cartResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data          = AddCartData::from($request);
        // $data->user_id = null;
        
        if (auth()->check()) {
            $data->user_id = auth()->id();
        }
        $cart          = $this->cartService->createCart($data);
        $this->cartSession->use($cart);

        foreach ($data->products as $prd) {

            $prod_var = ProductVariant::where('id', $prd['variant_id'])->first();

            if ($prod_var == null) {
                $response = new ErrorResponse('No Variant Found', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }

            if (!$this->ensureCartCurrencyMatchesVariant($cart, $prod_var)) {
                $response = new ErrorResponse('Selected variant has no price in any usable currency.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }

            if ($prod_var->storage_qty - $prd['qty'] < 0) {
                $response = new ErrorResponse('Sorry, not enought quantity to fill your order, We only have ' . $prod_var->storage_qty . ' Items.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }
            if ($prod_var->purchasable !== 'always') {
                $response = new ErrorResponse('This Product is not for sale.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }

        }
        $this->cartService->addProductsToCart($data, $cart);
        $cart->withTranslation = true;
        $cartResource          = new CartResource($cart);
        $response              = new SuccessResponse($cartResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function show(?int $id, Request $request)
    {
        if (auth()->check()) {
            $cartService = $this->cartService->getByUserId(Auth::user()->id);
        }
        else {
            $cartService = $this->cartService->getById($id);
        }
        if ($cartService) {
            $cartService->calculate();
            $cartService->withTranslation = $request->headers->has('translations');
        }

        $cartServiceResource = new CartResource($cartService);
        $response            = new SuccessResponse($cartServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function addToCart(int $id, Request $request)
    {

        $data = AddCartData::from($request);
        // if (!$data->user_id) {
        //     $data->user_id = auth()->user()->id;
        // }
        /** @var Cart $cartService * */
        $cartService = $this->cartService->getById($id);
        if (!$cartService) {
            $response = new ErrorResponse('Cart not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        
         // 🔥 Attach anonymous cart to logged-in user when possible
        if ($cartService->user_id === null && auth()->check()) {
            $cartService->user_id = auth()->id();
            $cartService->save();
        }
        

       // 🔥 Always attach cart to the logged-in user
        if (auth()->check()) {
            if ($cartService->user_id !== auth()->id()) {
                $cartService->user_id = auth()->id();
                $cartService->save();
            }
        }
        
        foreach ($data->products as $product) {
            $prod = ProductVariant::where('id', $product['variant_id'])->first();
            if (!$prod) {
                $response = new ErrorResponse('No Variant Found', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }

            if (!$this->ensureCartCurrencyMatchesVariant($cartService, $prod)) {
                $response = new ErrorResponse('Selected variant has no price in any usable currency.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }

            if ($prod->purchasable !== 'always') {
                $response = new ErrorResponse('This Product is not for sale.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }


            $line = CartLine::where('purchasable_id', $prod->id)->where('cart_id', $cartService->id)->first();

            if ($line && $line->purchasable->storage_qty - $product['qty'] < 0) {
                $response = new ErrorResponse('Sorry, not enought quantity to fill your order, We only have ' . $line->purchasable->storage_qty . ' Items.', Response::HTTP_NOT_ACCEPTABLE);

                return response()->error($response);
            }
            if (!$line) {
                $cartService->lines()->create([
                    'cart_id' => $cartService->id,
                    'purchasable_type' => ProductVariant::class,
                    'purchasable_id' => $prod->id,
                    'quantity' => $product['qty']
                ]);
            }
            else {
                if ($product['qty'] === 0) {
                    $cartService->remove($line->id, true);
                }
                else {
                    $cartService->updateLine($line->id, $product['qty']);
                }
            }

        }
        $cartService->calculate();
        $cartService->withTranslation = $request->headers->has('translations');
        $cartServiceResource          = new CartResource($cartService);
        $response                     = new SuccessResponse($cartServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    private function resolveCartCurrencyForVariant($cart, ProductVariant $variant): ?int
    {
        $currentCurrencyId = $cart->currency_id;

        if ($currentCurrencyId && $variant->prices()->where('currency_id', $currentCurrencyId)->exists()) {
            return (int) $currentCurrencyId;
        }

        $variantCity = City::find($variant->city_id);
        $cityCurrencyId = $variantCity?->currency_id;
        if ($cityCurrencyId && $variant->prices()->where('currency_id', $cityCurrencyId)->exists()) {
            return (int) $cityCurrencyId;
        }

        $firstPrice = $variant->prices()->first();

        return $firstPrice?->currency_id;
    }

    private function ensureCartCurrencyMatchesVariant($cart, ProductVariant $variant): bool
    {
        $resolvedCurrencyId = $this->resolveCartCurrencyForVariant($cart, $variant);
        if (!$resolvedCurrencyId) {
            return false;
        }

        if ((int) $cart->currency_id !== (int) $resolvedCurrencyId) {
            $cart->update(['currency_id' => $resolvedCurrencyId]);
        }

        return $variant->prices()->where('currency_id', $resolvedCurrencyId)->exists();
    }

    public function removeFromCart(int $id, Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:lunar_products,id',
        ]);
        $product     = ProductVariant::where('product_id', $request->product_id)->where('id', $request->variant_id)->first();
        $cartService = $this->cartService->getById($id);
        $line        = CartLine::where('purchasable_id', $product->id)
                               ->where('cart_id', $id)->first();

        $cartService->remove($line->id);
        $cartService->withTranslation = $request->headers->has('translations');
        $cartServiceResource          = new CartResource($cartService);
        $response                     = new SuccessResponse($cartServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }
        // public function update(Request $request, $id)
        // {
        //     $data                         = UpdateCartData::from($request);
        //     $cartService                  = $this->cartService->updateCart($id, $data);
        //     $cartService->withTranslation = true;
        //     $cartServiceResource          = new CartResource($cartService);
        //     $response                     = new SuccessResponse($cartServiceResource, Response::HTTP_OK);
    
        //     return response()->success($response);
        // }


    public function destroy($id)
    {
        $this->cartService->deleteCart($id);
        $response = new SuccessResponse("cart is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function usePoints(Request $request)
    {

        $data = UsePointsData::from($request);
        if (auth()->check()) {
            $user = Auth::user();
        }
        else {
            $response = new ErrorResponse('Please Login to use your points', Response::HTTP_FORBIDDEN);

            return response()->error($response);
        }
        $user_points = $user->rewardPoints()->first();
        if (!$user_points || $data->points_to_use > $user_points->points || $user_points->points < 1) {
            $response = new ErrorResponse('You dont have enough points', Response::HTTP_BAD_REQUEST);

            return response()->error($response);
        }
        $cart = $this->cartService->getByUserId($user->id);
        if ($cart->coupon_code) {
            $response = new ErrorResponse('You cant use points with coupon codes.', Response::HTTP_BAD_REQUEST);

            return response()->error($response);
        }
        $cart->points_used = $data->points_to_use;
        $cart->save();
        $point_price        = floatval($this->settingsService->getByName('point_price')->value);
        $cart->total->value = $cart->total->value - ($cart->points_used * $point_price);

        if ($cart->total->value < 0) {
            $response = new ErrorResponse('The used points exceed the cart total, resulting in a negative balance. Please adjust the points used.', Response::HTTP_BAD_REQUEST);

            return response()->error($response);
        }

        $cartResource = new CartResource($cart);
        $response     = new SuccessResponse($cartResource, Response::HTTP_OK);

        return response()->success($response);
    }

}
