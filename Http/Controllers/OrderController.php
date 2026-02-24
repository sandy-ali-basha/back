<?php

namespace App\Http\Controllers;
use App\Http\Data\AddCartData;
use App\Http\Data\AddCustomerData;
use App\Http\Data\AddOrderData;
use App\Http\Data\UpdateOrderData;
use App\Http\Data\UpdateOrderStatusData;
use App\Http\Helpers\FibApi;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Response\ErrorResponse;
use App\Http\Response\SuccessResponse;
use App\Models\OrderModel;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\City;
use App\Services\CartService;
use App\Models\AddressModel;
use App\Services\CustomerService;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Lunar\Facades\ShippingManifest;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Lunar\Models\ProductVariant;
use App\Models\Customer;
use Lunar\Models\Order;
use TCPDF;
use TCPDF_FONTS;
use Lunar\Models\TaxClass;
// use Lunar\Models\Transaction;
use App\Models\Transaction;
use App\Services\AddressService;
use App\Http\Controllers\FirebaseController;

use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{

    public OrderService $orderService;
    public CartService  $cartService;
    public CustomerService $customerService;
    public AddressService $addressService;
    public FirebaseController $firebaseController;

    public function __construct(OrderService $orderService, CartService $cartService, CustomerService $customerService,AddressService $addressService, FirebaseController $firebaseController)
    {
        $this->orderService = $orderService;
        $this->cartService = $cartService;
        $this->customerService = $customerService;
        $this->addressService = $addressService;
        $this->firebaseController = $firebaseController;

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders        = $this->orderService->getAllOrders();
        $orderResource = new OrderCollection($orders);
        $response      = new SuccessResponse($orderResource, Response::HTTP_OK);

        return response()->success($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /** @var Customer $customer **/
        $userId                 = Auth::user()->id;
        $customer = $this->customerService->getByUserId($userId);

        /** @var Cart $cart **/
        $cart           = Cart::find($request->cart_id);

        if ($cart->order()->first()) {
            $response = new ErrorResponse('An order is already  created  for this  cart', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }


        $selectedAddress = $request->address_id
            ? $customer->addresses()->where('id', $request->address_id)->first()
            : $customer->addresses()->first();

        if (!$selectedAddress) {
            $response = new ErrorResponse('Delivery address not found.', Response::HTTP_NOT_ACCEPTABLE);

            return response()->error($response);
        }

        $cart->addAddress($selectedAddress, 'billing');
        $cart->addAddress($selectedAddress, 'shipping');

        $firstLine = $cart->lines->first();
        $cityId = $firstLine?->purchasable?->city_id;

        if (!$cityId) {
            $response = new ErrorResponse('Unable to determine product city for checkout.', Response::HTTP_NOT_ACCEPTABLE);

            return response()->error($response);
        }

        if (!$this->cartService->checkAddressCity(collect([$selectedAddress]), $cityId)) {
            $response = new ErrorResponse('Please add a delivery address from the same city of the products.', Response::HTTP_NOT_ACCEPTABLE);

            return response()->error($response);
        }
        
        $taxClass = TaxClass::first();

        $shippingOption =  ShippingManifest::addOption(
            new ShippingOption(
                name: 'Basic Delivery',
                description: 'A basic delivery option',
                identifier: 'BASDEL',
                price: new Price(0, $cart->currency, 1),
                taxClass: $taxClass
            )
        );
        try {
            DB::beginTransaction();
            
            $cart->setShippingOption($shippingOption->options[0]);
            Auth::user()->decreasePoints($cart->points_used ?? 0);
         
         
        
            /** @var Order $order **/
            $order                  = $this->orderService->createOrder($cart);
            $this->sendTelegramOrderNotification($order); // Send the Telegram notification

            $success = Payments::driver($request->payment_method)->cart($cart)->authorize();
            DB::commit();
            if ($success->success) {
                    $this->firebaseController->addNotificationToUser(
                    $userId = Auth::id(),
                    $title = 'تم إنشاء طلب جديد',
                    $body = "تم إنشاء الطلب رقم {$order->id} بنجاح",
                    $type = 'order'
                );
                $paymentData = $request->payment_method !== 'cash-in-hand' ? $success->data : null;
                $order->withTranslation = true;
                $orderResource          = new OrderResource($order, $paymentData);
                $response               = new SuccessResponse($orderResource, Response::HTTP_OK);
                return response()->success($response);
            } else {
                $response = new ErrorResponse($success->message, Response::HTTP_BAD_REQUEST);
    
                return response()->error($response);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("{$e->getFile()}: ".$e->getMessage());
            $response = new ErrorResponse('An Error Occured', Response::HTTP_INTERNAL_SERVER_ERROR);
    
            return response()->error($response);
        }
    }
    
    function sendTelegramOrderNotification($order)
{
    $token = '7880047640:AAH_KUADzuTP8wJapGVDrILIM-e9rBzSzT0';
    $chat_id = '5564512355';
    $orderId = $order->id;
    
    // Get only the specific data you want to send
    $orderData = [
        'id' => $order->id,
        'status' => $order->status,
        'reference' => $order->reference,
        'subtotal' => $order->subtotal,
        'total' => $order->total,
        'shipping_total' => $order->shipping_total,
        'sub_total_after_points' => $order->sub_total_after_points,
        'points_used' => $order->points_used,
        'point_price' => $order->point_price
    ];

    // Create the message with the extracted data
    $message = "🛒 *New Order #$orderId*\n";
    foreach ($orderData as $key => $value) {
        // Only add non-empty values
        if (!empty($value)) {
            // Escape special characters to avoid Markdown parsing issues
            $formattedValue = $this->escapeMarkdownSpecialChars($value);
            $message .= ucfirst(str_replace('_', ' ', $key)) . ": $formattedValue\n";
        }
    }

    // Create the inline keyboard buttons for order statuses
    $statuses = config('lunar.orders.statuses');
    $keyboard = [];
    foreach ($statuses as $statusKey => $statusArr) {
        $keyboard[] = [
            [
                'text' => $statusArr['label'],
                'callback_data' => "status_{$statusKey}_order_{$orderId}"
            ]
        ];
    }

    // Prepare the request data with inline keyboard
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        'parse_mode' => 'Markdown'
    ];

    // Send the notification to Telegram via cURL
    $ch = curl_init("https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Log the response and any error
    if ($response) {
        Log::info('Telegram Response:', ['response' => $response]);
    } else {
        Log::error('Telegram Error:', ['error' => $error]);
    }
}
     public function storeAdminOrder(Request $request)
{
    $request->validate([
        'city_id'                => 'required',
        'customer.first_name'          => 'required|string',
        'customer.last_name'          => 'required|string',

        'customer.phone'         => 'required|string',
        'customer.address'       => 'required|string',
        'customer.notes'         => 'nullable|string',
        'products'                  => 'required|array|min:1',
        'products.*.variant_id'     => 'required',
        'products.*.qty'       => 'required|integer|min:1',
        'products.*.price'          => 'required|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Create or Get Customer
        |--------------------------------------------------------------------------
        */
        $customerData = $request->input('customer');

        $customer = Customer::firstOrCreate(
            ['phone_number' => $customerData['phone']],
            [
                'first_name'  => $customerData['first_name'],
                'last_name'  => $customerData['last_name'],
                'notes' => $customerData['notes'] ?? null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Create Address
        |--------------------------------------------------------------------------
        */
        $address = $customer->addresses()->create([
            'first_name'=>$customer->first_name,
            "last_name"=>$customer->last_name,
            'city' => $request->city_id,
            'line_one' => $customerData['address'],
            "contact_email"=>$request->customer["contact_email"],
            "contact_phone"=>$customer->contact_phone,
            "shipping_default"=>1,
            "billing_default"=> 1,
            "country_id"=>2
        ]);
        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Create Cart
        |--------------------------------------------------------------------------
        */
        
        $cart = Cart::create([
            'currency_id' => $request->currency_id, // عدّل حسب سيستمك
            'channel_id'=>1,
        ]);

        foreach ($request->products as $item) {
            $cart->lines()->create([
                'cart_id' => $cart->id,
                'purchasable_id'   => $item['variant_id'],
                'purchasable_type' => ProductVariant::class,
                'quantity'         => $item['qty']
                ]);
        }

        $cart->addAddress($address, 'billing');
           //     dd("hiyyi");

        $cart->addAddress($address, 'shipping');

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Shipping
        |--------------------------------------------------------------------------
        */
        $taxClass = TaxClass::first();
        $shippingOption = ShippingManifest::addOption(
            new ShippingOption(
                name: 'Basic Delivery',
                description: 'Admin Order Delivery',
                identifier: 'ADMIN_DELIVERY',
                price: new Price(0, $cart->currency, 1),
                taxClass: $taxClass
            )
        );

        $cart->setShippingOption($shippingOption->options[0]);

        /*
        | 5️⃣ Create Order
        |--------------------------------------------------------------------------
        */

        $order = $this->orderService->createOrder($cart);
        $order->update([
            'customer_id' => $customer->id,
            'user_id'     => null,
            'notes'       => $customerData['notes'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Payment (Cash example)
        |--------------------------------------------------------------------------
        */

        $success = Payments::driver('cash-in-hand')
            ->cart($cart)
            ->authorize();

        if (!$success->success) {
            DB::rollBack();
            return response()->error(
                new ErrorResponse($success->message, Response::HTTP_BAD_REQUEST)
            );
        }

        DB::commit();

        $order->withTranslation = true;
        $orderResource = new OrderResource($order);
    $this->firebaseController->addNotificationToUser(
                    $userId = Auth::id(),
                    $title ='تم إنشاء طلب جديد من لوحة التحكم',
                    $body = "تم إنشاء الطلب رقم {$order->id} بنجاح",
                    $type = 'order'
     );
        return response()->success(
            new SuccessResponse($orderResource, Response::HTTP_OK)
        );
       
    } catch (\Exception $e) {
     DB::rollBack();
            Log::error("{$e->getFile()}: ".$e->getMessage());
            $response = new ErrorResponse('An Error Occured', Response::HTTP_INTERNAL_SERVER_ERROR);
    
            return response()->error($response);
    }
}


//   public function storeAdminOrder(Request $request)
//     {
//         /** @var Customer $customer **/
//         $userId                 = $request->user_id;
//         $customer = $this->customerService->getByUserId($userId);
//         if($customer == null){
//             $dataCustomer = ["first_name"=>$request->customer["name"], "last_name"=>$request->customer["name"] , "user_id"=> $userId,"phone_number"=>$request->customer["phone_number"], "gender"=> $request->customer["gender"],"age"=>$request->customer["age"] ];
//              $customerData          = AddCustomerData::from($dataCustomer);
//              $customerData = $this->customerService->createNewCustomer($customerData);
            
//         }

//         /** @var Cart $cart **/
//         // $cart = Cart::where("user_id", $user_id)->whereDoesntHave("order")->first();
//         // if ($cart) {
//         //     $response = new ErrorResponse('An order is already  created  for this  cart', Response::HTTP_NOT_FOUND);

//         //      return response()->error($response);
//         //  }
//         $data = [
//             "country_id"=>2,
//             "currency_id"=>$request->currency_id,
//             "user_id"=>$userId,
//             "products"=> $request->products
//             ];
//         $dataCart = AddCartData::from($data);
//         $cart = $this->cartService->createCart($dataCart);
//         dd($cart);
//         if($customer->addresses()->first() == null ){
//             $addressData = [
//                 "country_id"=> 2,
//                 "customer_id"=>$customer->id,
//                 "first_name"=> $request->customer["first_name"], 
//                 "last_name"=> $request->customer["last_name"],
//                 "city"=> "Syria",
//                 "state"=> $request->customer["state"], 
//                 "contact_email"=> $request->customer["contact_email"], 
//                 "contact_phone"=> $request->customer["contact_phone"],
//                 "shipping_default"=> $request->customer["shipping_default"], 
//                 "billing_default"=> $request->customer["billing_default"], 
//                 "delivery_instructions"=> $request->customer["notes"],
//                 "line_one" => $request->customer["address"]
//                 ];
//             AddressModel::create($addressData);
//         }
//         $cart->addAddress($customer->addresses()->first(), 'billing');
//         $cart->addAddress($customer->addresses()->first(), 'shipping');

//         if ($request->address_id) {
//             $cart->addAddress($customer->addresses()->where('id', $request->address_id)->first(), 'billing');
//             $cart->addAddress($customer->addresses()->where('id', $request->address_id)->first(), 'shipping');
//         }
//     $products = collect($request->products)->map(fn ($item) => [
//         'variant_id' => $item['variant_id'],
//         'qty' => $item['quantity'],
//     ])->toArray();

//     $cartData = new AddCartData(
//         user_id: $userId,
//         coupon_code: null,
//         products: $products,
//         currency_id: $request->currency_id);
 
//     $this->cartService->addProductsToCart($cartData, $cart);

//         $cityId = City::find($cart->lines[0]->purchasable->city_id)->id;
//         Log::alert($this->cartService->checkAddressCity($cart->addresses, $cityId));
//         dd($cart->addresses);
//         if (!$this->cartService->checkAddressCity($cart->addresses, $cityId)) {

//             $response = new ErrorResponse('Please add a delivery address from the same city of the products.', Response::HTTP_NOT_ACCEPTABLE);

//             return response()->error($response);
//         }
//         $taxClass = TaxClass::first();
        
//         $shippingOption =  ShippingManifest::addOption(
//             new ShippingOption(
//                 name: 'Basic Delivery',
//                 description: 'A basic delivery option',
//                 identifier: 'BASDEL',
//                 price: new Price(0, $cart->currency, 1),
//                 taxClass: $taxClass
//             )
//         );
//         try {
//             DB::beginTransaction();
//             $cart->setShippingOption($shippingOption->options[0]);
//             Auth::user()->decreasePoints($cart->points_used ?? 0);
         
         
        
//             /** @var Order $order **/
//             $order                  = $this->orderService->createOrder($cart);
    
//             $success = Payments::driver($request->payment_method)->cart($cart)->authorize();
//             DB::commit();
//             if ($success->success) {
//                 $paymentData = $request->payment_method !== 'cash-in-hand' ? $success->data : null;
//                 $order->withTranslation = true;
//                 $orderResource          = new OrderResource($order, $paymentData);
//                 $response               = new SuccessResponse($orderResource, Response::HTTP_OK);
//                 return response()->success($response);
//             } else {
//                 $response = new ErrorResponse($success->message, Response::HTTP_BAD_REQUEST);
    
//                 return response()->error($response);
//             }
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error("{$e->getFile()}: ".$e->getMessage());
//             $response = new ErrorResponse('An Error Occured', Response::HTTP_INTERNAL_SERVER_ERROR);
    
//             return response()->error($response);
//         }
//     }


    public function myOrders()
    {
        $userId                 = Auth::user()->id;
        $orders = OrderModel::where('user_id', $userId)->get();
        $orderResource          = new OrderCollection($orders);
        $response               = new SuccessResponse($orderResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function show($id, Request $request)
    {
        $orderService = $this->orderService->getById($id);
        if (!$orderService) {
            $response = new ErrorResponse('Order not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $orderService->withTranslation = $request->headers->has('translations');
        $orderServiceResource          = new OrderResource($orderService);
        $response                      = new SuccessResponse($orderServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function update(Request $request, $id)
    {
        $data                          = UpdateOrderData::from($request);
        $orderService                  = $this->orderService->updateOrder($id, $data);
        $orderService->withTranslation = true;
        $orderServiceResource          = new OrderResource($orderService);
        $response                      = new SuccessResponse($orderServiceResource, Response::HTTP_OK);

        return response()->success($response);
    }


    public function destroy($id)
    {
        $this->orderService->deleteOrder($id);
        $response = new SuccessResponse("order is deleted", Response::HTTP_OK);

        return response()->success($response);
    }

    public function updatePayment(Request $request) {
        $intentTransaction = Transaction::WhereType('intent')->where('reference', $request->id)->first();
        

        if (!$intentTransaction) {
            $response = new ErrorResponse('No Order Exist for the requested id.', 406);
            return response()->error($response);
        }

        try {
            DB::beginTransaction();
          
            $intentTransaction->update([
                'success' => true,
                'status' => $request->status,
                'type' => 'capture'
            ]);

            // $intentTransaction->order->first()->update([
            //     'status' => 'payment-received',
            //     'placed_at' => now()
            // ]);
            
            DB::commit();
            $response = new SuccessResponse('Invoice Updated Successfully!', 200);

        } catch (\Exception $e) {
            DB::rollBack();   
            Log::error($e->getMessage());
            $response = new ErrorResponse('General Error', 500);
            return response()->error($response);
        }
        
    }


    public function updateStatus(Request $request, $id) {
        $order = $this->orderService->getById($id);
        if (!$order) {
            $response = new ErrorResponse('Order not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }
        $request->merge(['id' => $id]);
        $data = UpdateOrderStatusData::from($request);
        $order = $this->orderService->updateOrderStatus($id, $data->status);
        
        $orderResource = new OrderResource($order);
        $response = new SuccessResponse($orderResource, Response::HTTP_OK);

        return response()->success($response);
    }

    public function cancelOrder($id) {
        $order = $this->orderService->getById($id);
        
        if (!$order) {
            $response = new ErrorResponse('Order not found', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        if (!$order->canCancel()) {
            $response = new ErrorResponse('Sorry, Cant Cancel the order, it is already paid or already processed.', Response::HTTP_NOT_FOUND);

            return response()->error($response);
        }

        $order->update(['status' => 'cancel_requested']);
        $response = new SuccessResponse(['message' => "Order Cancel Requested Successfully !"], Response::HTTP_OK);

        return response()->success($response);
    }

       public function getInvoice(Request $request, $id,$lang)
    {
        // Fetch the order using your service\

        $order = $this->orderService->getById($id);
      
        if (!$order) {
            $response = new ErrorResponse('Order not found', Response::HTTP_NOT_FOUND);
            return response()->error($response);
        }

        // Initialize TCPDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Application');
        $pdf->SetTitle("Order Invoice - {$order->reference}");
        $pdf->SetSubject('Order Invoice');
        $pdf->SetKeywords('Invoice, TCPDF, PDF');

        // Set margins and auto page breaks
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 25);

        // Disable the default header and footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage();

        if ($lang=='kr'){
    
            $fontPath = public_path('fonts/NotoNaskhArabic-Regular.ttf');
            TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

            $pdf->SetFont('NotoNaskhArabic', '', 18);

            // Enable RTL for Arabic text
            $pdf->setRTL(true);
            $html = view('adminhub::orderkr', [
                'order' => $order,
            ])->render();
        }
        elseif($lang=='ar'){
            
            $fontPath = public_path('fonts/NotoNaskhArabic-Regular.ttf');
            TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

            $pdf->SetFont('NotoNaskhArabic', '', 18);

            // Enable RTL for Arabic text
            $pdf->setRTL(true);
            $html = view('adminhub::orderar', [
                'order' => $order,
            ])->render();
        }
        elseif($lang=='en'){
            $fontPath = public_path('fonts/NotoNaskhArabic-Regular.ttf');
            TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);

            $pdf->SetFont('NotoNaskhArabic', '', 18);

            // Enable RTL for Arabic text
            $pdf->setRTL(false);
            $html = view('adminhub::orderen', [
                'order' => $order,
            ])->render();
        }



        // Write the HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF (stream it to the browser)
        $pdf->Output("Order-{$order->reference}.pdf", 'I');
    }
  private function updateOrderStatus($orderId, $status)
{
    $order = Order::find($orderId);

    if ($order) {
        // Log the current status before updating
        Log::info("Order #$orderId status before update: {$order->status}");

        // Check if the status is valid before updating
        if (array_key_exists($status, config('lunar.orders.statuses'))) {
            // Capture the old status before changing it
            $oldStatus = $order->status;

            // Update the order status
            $order->status = $status;
            $order->save(); // Save the updated order status to the database

            // Log the new status after the update
            Log::info("Order #$orderId status updated from '$oldStatus' to '$status'");

        } else {
            Log::error("Invalid status '$status' for Order #$orderId");
        }
    } else {
        Log::error("Order with ID #$orderId not found.");
    }
}

    // Function to edit the message in Telegram after status update
    private function editTelegramMessage($chat_id, $message_id, $text)
    {
    $token = '7880047640:AAH_KUADzuTP8wJapGVDrILIM-e9rBzSzT0';
    $url = "https://api.telegram.org/bot$token/editMessageText";
        $data = [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'parse_mode' => 'Markdown'
        ];

        // Make the HTTP request to edit the Telegram message
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_exec($ch);
        curl_close($ch);
    }
    
    
private function sendTelegramMessage($chat_id, $message)
{
    $token = '7880047640:AAH_KUADzuTP8wJapGVDrILIM-e9rBzSzT0';
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    // Send the message to Telegram via cURL
    $ch = curl_init("https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Log the response and any error
    if ($response) {
        Log::info('Telegram Response:', ['response' => $response]);
    } else {
        Log::error('Telegram Error:', ['error' => $error]);
    }
}
public function handle(Request $request)
{
    // Capture the incoming update from Telegram
    $update = $request->all();

    // Log the entire update for debugging purposes
    Log::info("Received update from Telegram", ['update' => $update]);

    // Check if the update contains a callback query (button press)
    if (isset($update['callback_query'])) {
        $callback = $update['callback_query'];
        $chat_id = $callback['message']['chat']['id'];
        $message_id = $callback['message']['message_id'];
        $data = $callback['data']; // e.g., "status_order_processing_order_185"

        // Log the callback query data for further inspection
        Log::info("Received callback query from Telegram", [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'data' => $data
        ]);

        // Parse the callback data to get the status and order ID
        if (preg_match('/^status_(.+)_order_(\d+)$/', $data, $matches)) {
            $statusKey = $matches[1];  // e.g., "order_requested"
            $orderId = $matches[2];    // e.g., "185"

            // Log the parsed data
            Log::info("Parsed callback data successfully", [
                'status' => $statusKey,
                'order_id' => $orderId
            ]);

            // Proceed to update the order status
            $this->updateOrderStatus($orderId, $statusKey);

            // Get the status label for the message
            $statuses = config('lunar.orders.statuses');
            $statusLabel = $statuses[$statusKey]['label'] ?? $statusKey;

            // Create the message for the new status
            $msg = "Order #$orderId has been updated to: *{$statusLabel}*";

            // Send the new message on Telegram
            $this->sendTelegramMessage($chat_id, $msg);

            // Log the successful message update
            Log::info("Telegram message sent for order #$orderId", [
                'status' => $statusLabel,
                'chat_id' => $chat_id
            ]);
        } else {
            // If the callback data format doesn't match, log an error
            Log::error("Invalid callback data format", ['data' => $data]);
        }
    } else {
        // Log if there is no callback query in the incoming update
        Log::error("No callback query found in the update", ['update' => $update]);
    }

    return response('OK', 200);
}

}