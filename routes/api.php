<?php
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AccordionController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CareerCategoryController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductAttributesController;
use App\Http\Controllers\ProductAttributesValuesController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOptionController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaxClassController;
use App\Http\Controllers\TaxRateController;
use App\Http\Controllers\TaxZoneController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteSettingsController;
use Illuminate\Http\Request;
use App\Http\Controllers\RegionController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\ContactInfoController;
use App\Http\Controllers\LocationsController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\PharmacyController;


Route::middleware(['auth:sanctum'])->get('/user', function(Request $request) {
    return $request->user();
});


Route::post('/telegram/webhook', [OrderController::class, 'handle']);


// --- Tax Classes (lunar_tax_classes) ---
Route::prefix('tax-classes')->name('tax-classes.')->group(function () {
    // CRUD: index, store, show, update, destroy
    Route::apiResource('/', TaxClassController::class)->except(['store', 'update','show']);

    // Store and Update must be defined explicitly to prevent route name conflicts
    // when using the root path for apiResource.
    Route::get('/{id}', [TaxClassController::class, 'show'])->name('show');
    Route::post('/', [TaxClassController::class, 'store'])->name('store');
    Route::put('{taxClass}', [TaxClassController::class, 'update'])->name('update');

    // Custom Endpoint for deep relationship data
    Route::get('{taxClass}/rates-and-zones', [TaxClassController::class, 'getRatesAndZones'])
         ->name('rates-and-zones');
});

// --- Tax Rates (lunar_tax_rates) ---
Route::prefix('tax-rates')->name('tax-rates.')->group(function () {
    // CRUD: index, show, update, destroy
    // The 'store' method is complex (needs zone/state creation) and defined separately below.
    Route::apiResource('/', TaxRateController::class)->except(['store']);

    // Complex Store: Creates Rate, Zone, and links State in one transaction
    Route::post('/', [TaxRateController::class, 'store'])->name('store');
});

// --- Tax Zones (lunar_tax_zones) ---
// Standard resource routes handle all CRUD operations (index, store, show, update, destroy)
Route::apiResource('tax-zones', TaxZoneController::class);
Route::get('/products/{id}/images/{type}', [ProductImageController::class, 'index']);        // View images
Route::post('/products/{id}/images/{type}', [ProductImageController::class, 'store']);        // Add images
Route::delete('/products/{id}/images/{imageId}/{type}', [ProductImageController::class, 'destroy']); // Delete an image
Route::post('/products/{id}/images/{imageId}/{type}', [ProductImageController::class, 'update']); // Replace an image
Route::apiResource('currencies', CurrencyController::class);

Route::post('/register', [UserController::class, 'register']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/login', [UserController::class, 'login']);
Route::get('/user/my-points', [UserController::class, 'myPoints']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password/{token}', [UserController::class, 'resetPassword'])->middleware('guest')->name('password.reset');

Route::post('/change-password', [UserController::class, 'changePassword']);
Route::post('/verify-account', [UserController::class, 'verifyAccount'])->middleware('guest')->name('verify');
//Route::get('/auth/{provider}/redirect', [UserController::class, 'socialLogin'])->middleware('guest')->name('auth.social.redirect');
Route::post('/auth/{provider}/callback', [UserController::class, 'socialCallback'])->middleware('guest')->name('auth.social.callback');
Route::prefix('admin')->middleware(['role:super_admin|super_admin'])->group(function() {
    Route::get('/', [AdminController::class, 'index']);
    Route::post('/', [AdminController::class, 'store']);
    Route::get('/{id}', [AdminController::class, 'show']);
    Route::post('/{id}', [AdminController::class, 'update']);
    Route::delete('/{id}', [AdminController::class, 'destroy']);
});

Route::prefix('careers')->middleware('language')->group(function() {
    Route::get('/', [CareerController::class, 'index']);
    Route::get('/{id}', [CareerController::class, 'show']);
    Route::middleware(['role:super_admin|website_admin'])->group(function (){
        Route::post('/', [CareerController::class, 'store']);
        Route::delete('/{id}', [CareerController::class, 'destroy']);
        Route::post('/{id}', [CareerController::class, 'update']);
    });
});

Route::prefix('accordion')->middleware('language')->group(function() {
    Route::get('/{id}', [AccordionController::class, 'show']);
    Route::middleware(['role:super_admin|website_admin'])->group(function (){
        Route::post('/', [AccordionController::class, 'store']);
        Route::delete('/{id}', [AccordionController::class, 'destroy']);
        Route::post('/{id}', [AccordionController::class, 'update']);
    });
});

Route::prefix('careers_categories')->middleware('language')->group(function() {
    Route::get('/', [CareerCategoryController::class, 'index']);
    Route::get('/{id}', [CareerCategoryController::class, 'show']);
    Route::middleware(['role:super_admin|website_admin'])->group(function (){
        Route::post('/', [CareerCategoryController::class, 'store']);
        Route::delete('/{id}', [CareerCategoryController::class, 'destroy']);
        Route::post('/{id}', [CareerCategoryController::class, 'update']);
    });
});

Route::prefix('terms')->middleware('language')->group(function() {
    Route::get('/', [WebsiteSettingsController::class, 'index']);
    Route::get('/{id}', [WebsiteSettingsController::class, 'getTermsAndConditions']);
    Route::middleware(['role:super_admin|website_admin'])->group(function (){
        Route::post('/{id}', [WebsiteSettingsController::class, 'updateTermsAndConditions']);
        Route::post('/', [WebsiteSettingsController::class, 'storeTermsAndConditions']);
        Route::delete('/{id}', [WebsiteSettingsController::class, 'destroy']);
    });
});
Route::post('/contact', [ContactController::class, 'sendContactForm']);
Route::prefix('blog')->middleware('language')->group(function() {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::middleware(['role:super_admin|website_admin'])->group(function (){
        Route::post('/', [PostController::class, 'store']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::post('/{id}', [PostController::class, 'update']);
    });
});

Route::middleware(['role:super_admin|website_admin'])->prefix('notifications')->group(function () {
    Route::post('/', [FirebaseController::class, 'addNotificationToUser']);
    Route::get('/', [FirebaseController::class, 'getUserNotifications']);
    Route::get('/unread-count', [FirebaseController::class, 'unreadCount']);
    Route::patch('/read', [FirebaseController::class, 'markAllAsRead']);
    Route::patch('/{notificationId}/deactivate', [FirebaseController::class, 'deactivateNotification']);
    Route::get('/check-reorder', [FirebaseController::class, 'checkReorderPointsAndNotify']);

});

Route::middleware(['role:super_admin|website_admin'])->group(function (){ 
    Route::post('blog/image/{id}', [PostController::class, 'addImageToPost']);
    Route::delete('blog/image/{id}', [PostController::class, 'deletePostImage']);
});

   Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers/{id}', [CustomerController::class, 'update']);
Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

Route::prefix('brand')->middleware('language')->group(function() {
    Route::get('/', [BrandController::class, 'index']);
    Route::get('/{id}', [BrandController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [BrandController::class, 'store']);
        Route::delete('/{id}', [BrandController::class, 'destroy']);
        Route::post('/{id}', [BrandController::class, 'update']);
    
        Route::post('/image/{id}', [BrandController::class, 'addImageToBrand']);
    });

});



Route::prefix('discounts')->middleware('language')->group(function() {
    Route::get('/', [DiscountController::class, 'index']);
    Route::get('/{id}', [DiscountController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [DiscountController::class, 'store']);
        Route::delete('/{id}', [DiscountController::class, 'destroy']);
        Route::post('/{id}', [DiscountController::class, 'update']);
    });
});
Route::prefix('brand_pages')->middleware('language')->group(function() {
    Route::get('/{id}', [BrandController::class, 'getBrandPage']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [BrandController::class, 'createBrandPages']);
        Route::post('/slides', [BrandController::class, 'addPageSlides']);
        Route::post('/{id}', [BrandController::class, 'updateBrandPage']);
    
        Route::delete('/{id}', [BrandController::class, 'deleteBrandPage']);
    
    
        Route::delete('/slides/{id}', [BrandController::class, 'deleteSlide']);
    });

});

Route::post('/coupon', [CartController::class, 'useDiscount']);
Route::post('/use-points', [CartController::class, 'usePoints']);
Route::prefix('product_type')->middleware('language')->group(function() {
    Route::get('/', [ProductTypeController::class, 'index']);
    Route::get('/{id}', [ProductTypeController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [ProductTypeController::class, 'store']);
        Route::delete('/{id}', [ProductTypeController::class, 'destroy']);
        Route::post('/{id}', [ProductTypeController::class, 'update']);
    });
});
Route::prefix('cart')->middleware('language')->group(function() {
    Route::post('/', [CartController::class, 'store']);
    Route::get('/{id}', [CartController::class, 'show']);
    Route::post('/{id}', [CartController::class, 'addToCart']);
    Route::delete('/{id}', [CartController::class, 'removeFromCart']);
});

Route::prefix('order')->middleware('language')->group(function() {
    Route::get('/my_orders', [OrderController::class, 'myOrders']);
    Route::get('/my_orders/{id}', [OrderController::class, 'checkPaymentStatus']);
    Route::post('/my_orders/{id}/change-payment', [OrderController::class, 'changePayment']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/order_data', [OrderController::class, 'orderData']);
    Route::post('/{id}/cancel', [OrderController::class, 'cancelOrder']);
   
    Route::middleware(['role:super_admin|ecommerce_admin|order_admin'])->group(function (){
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/{id}/update-status', [OrderController::class, 'updateStatus']);
        Route::post('/admin', [OrderController::class, 'storeAdminOrder']);

    });
    Route::get('/{id}/pdf/{lang}', [OrderController::class, 'getInvoice']);
});


Route::prefix('product')->middleware('language')->group(function() {
        Route::get('/', [ProductController::class, 'index']);
                Route::post('/', [ProductController::class, 'store']);

    Route::get('/offers', [ProductController::class, 'getOffers']);
    Route::get('/{product_id}/accordion', [AccordionController::class, 'index']);
    Route::get('/similar/{id}', [ProductController::class, 'similarProducts']);
    Route::get('/{id}', [ProductController::class, 'show']);
    Route::get('/slider/{id}', [ProductController::class, 'getImagesSlider']);
    Route::middleware([])->group(function (){
          Route::post('/{id}/purchasable ', [ProductController::class, 'updatePurchasable']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
        Route::post('/{id}', [ProductController::class, 'update']);
        Route::post('/slider/{id}', [ProductController::class, 'uploadImagesSlider']);
       Route::post('/updateslide/{id}', [ProductController::class, 'updateSliderImage']);

        Route::post('/image/{id}', [ProductController::class, 'addImageToProduct']);
        Route::delete('/image/{id}', [ProductController::class, 'deleteImage']);
        
        Route::get('/change-status/{id}', [ProductController::class, 'toggleProduct']);
        Route::get('/update-points-price/apply', [ProductController::class, 'updatePoints']);
    });
});
Route::get('/search-by-name', [ProductController::class, 'searchProducts']);

Route::prefix('product_attributes')->middleware('language')->group(function() {
    Route::get('/', [ProductAttributesController::class, 'index']);
    Route::get('/{id}', [ProductAttributesController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [ProductAttributesController::class, 'store']);
        Route::delete('/{id}', [ProductAttributesController::class, 'destroy']);
        Route::post('/{id}', [ProductAttributesController::class, 'update']);
        Route::post('/toggle-status/{id}', [ProductAttributesController::class, 'toggleStatus']);
        Route::get('/toggle-nav-status/{id}', [ProductAttributesController::class, 'toggleNavStatus']);


    });

});

Route::post('/attribute/{id}', [ProductController::class, 'addAttribute']);
Route::post('/filter', [ProductController::class, 'attributeFilter']);
Route::prefix('product_attributes_values')->middleware('language')->group(function() {
    Route::get('/attribute/{id}', [ProductAttributesValuesController::class, 'index']);
    Route::get('/attribute/website/{id}', [ProductAttributesValuesController::class, 'indexWebsite']);
    Route::get('/{id}', [ProductAttributesValuesController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [ProductAttributesValuesController::class, 'store']);
        Route::delete('/{id}', [ProductAttributesValuesController::class, 'destroy']);
        Route::post('/{id}', [ProductAttributesValuesController::class, 'update']);
    });
});
Route::prefix('addresses')->middleware(['language'])->group(function() {
    Route::get('/', [AddressController::class, 'index']);
    Route::get('/{id}', [AddressController::class, 'show']);
    Route::post('/', [AddressController::class, 'store']);
    Route::delete('/{id}', [AddressController::class, 'destroy']);
    Route::post('/{id}', [AddressController::class, 'update']);
});
    Route::prefix('product_options')->middleware('language')->group(function() {
    Route::get('/', [ProductOptionController::class, 'index']);
    Route::get('/value/{id}', [ProductOptionController::class, 'getValues']);
    Route::get('/{id}', [ProductOptionController::class, 'show']);
    Route::middleware([])->group(function (){
        Route::post('/', [ProductOptionController::class, 'store']);
        Route::delete('/{id}', [ProductOptionController::class, 'destroy']);
        Route::post('/{id}', [ProductOptionController::class, 'update']);
        Route::post('/value/{id}', [ProductOptionController::class, 'addValues']);
        Route::delete('/value/{id}', [ProductOptionController::class, 'destroyValue']);
    });
});

Route::post('product/variants/{id}', [ProductController::class, 'updateProductVariant']);
Route::get('product/variants/{id}', [ProductController::class, 'getProductVariants']);
Route::post('product/updatevariants/{id}', [ProductController::class, 'createVariant']);

Route::delete('product/variants/{id}', [ProductController::class, 'deleteVariant']);
Route::prefix('settings')->middleware('language')->group(function() {
    Route::get('/', [SettingController::class, 'index']);
    Route::get('/{name}', [SettingController::class, 'show']);
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/', [SettingController::class, 'update']);
    });
});

Route::prefix('payments')->middleware('language')->group(function() {
    Route::middleware(['role:super_admin|ecommerce_admin'])->group(function (){
        Route::post('/update', [OrderController::class,'updatePayment'])->name('transaction.update');
    });
});

    Route::get('/cities', [CitiesController::class, 'index']);
    Route::post('/cities/create', [CitiesController::class, 'store']);
    Route::post('/cities', [CitiesController::class, 'update']);
    Route::delete('/cities/{id}', [CitiesController::class, 'destroy']);
    
    //Halah Start
    Route::prefix('about')->group(function () {
    
        // ===== Partners (PUBLIC READ) =====
        Route::prefix('partners')->group(function () {
            Route::get('/', [PartnerController::class, 'index']);
            Route::get('/{id}', [PartnerController::class, 'show']);
    
            // CRUD (ADMIN ONLY)
            Route::middleware(['role:super_admin|website_admin'])->group(function () {
                Route::post('/', [PartnerController::class, 'store']);
                Route::post('/{id}', [PartnerController::class, 'update']);
                Route::delete('/{id}', [PartnerController::class, 'destroy']);
            });
        });
    
        // ===== About Us (PUBLIC READ) =====
        Route::get('/', [AboutUsController::class, 'index']);
        Route::get('/{id}', [AboutUsController::class, 'show']);
    
        // CRUD (ADMIN ONLY)
        Route::middleware(['role:super_admin|website_admin'])->group(function () {
            Route::post('/', [AboutUsController::class, 'store']);
            Route::post('/{id}', [AboutUsController::class, 'update']);
            Route::delete('/{id}', [AboutUsController::class, 'destroy']);
        });
    
    });


   Route::prefix('pharmacies')->group(function () {

        // PUBLIC ROUTES (READ ONLY)
        Route::get('/', [PharmacyController::class, 'index']);      // Get all pharmacies
        Route::get('{id}', [PharmacyController::class, 'show']);    // Get pharmacy by id
    
        // ADMIN ONLY ROUTES (CREATE / UPDATE / DELETE)
        Route::middleware(['role:super_admin|website_admin'])->group(function () {
            Route::post('/', [PharmacyController::class, 'store']);     // Create pharmacy
            Route::put('{id}', [PharmacyController::class, 'update']);  // Update pharmacy
            Route::delete('{id}', [PharmacyController::class, 'destroy']); // Delete pharmacy
        });
    
    });


    
    Route::prefix('contact_info')->group(function () {

        Route::prefix('locations')->group(function () {
    
            // PUBLIC
            Route::get('/', [LocationsController::class, 'index']);
            Route::get('/{id}', [LocationsController::class, 'show']);
    
            // ADMIN (SUPER + WEBSITE)
            Route::middleware(['role:super_admin|website_admin'])->group(function () {
                Route::post('/', [LocationsController::class, 'store']);
                Route::put('/{id}', [LocationsController::class, 'update']);
                Route::delete('/{id}', [LocationsController::class, 'destroy']);
            });
        });


        // PUBLIC
        Route::get('/', [ContactInfoController::class, 'index']);
        Route::get('/{id}', [ContactInfoController::class, 'show']);
    
        // UPDATE (SUPER + WEBSITE)
        Route::middleware(['role:super_admin|website_admin'])->group(function () {
            Route::put('/{id}', [ContactInfoController::class, 'update']);
        });

        // STORE + DELETE (SUPER ADMIN ONLY)
        Route::middleware(['role:super_admin'])->group(function () {
            Route::post('/', [ContactInfoController::class, 'store']);
            Route::delete('/{id}', [ContactInfoController::class, 'destroy']);
        });
    
    });
    //////End


    Route::post('/products/{id}/duplicate', [ProductController::class, 'duplicateProduct']);
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete']);
    Route::prefix('regions')->group(function () {
        Route::get('/', [RegionController::class, 'index']);               // List all regions
        Route::post('/', [RegionController::class, 'store']);              // Create a new region
        Route::get('/{id}', [RegionController::class, 'show']);            // Show a specific region
        Route::put('/{id}', [RegionController::class, 'update']);          // Update a region
        Route::delete('/{id}', [RegionController::class, 'destroy']);      // Delete a region
    
        // Manage cities linked to a region
        Route::get('/{id}/manage-cities', [RegionController::class, 'manageCities']);
        Route::post('/{id}/update-cities', [RegionController::class, 'updateCities']);
        Route::post('/create-city', [RegionController::class, 'createCity']);
            Route::post('/{id}/update-price', [ProductController::class, 'updateRegionPrice']);
    });
    Route::prefix('home')->middleware('language')->group(function () {
        Route::get('/slides', [HomeController::class, 'getHomeSlides']);
        Route::get('/settings', [HomeController::class, 'index']);
        //Halah
        Route::get('/tabs', [HomeController::class, 'getDashHomeTabs']);
        Route::get('/free_shipping', [HomeController::class, 'getFreeShipping']);

        Route::middleware(['role:super_admin|website_admin'])->group(function (){
            Route::post('/slides/add', [HomeController::class, 'addHomeSlide']);
            Route::post('/slides/update/{id}', [HomeController::class, 'editHomeSlide']);
            Route::delete('/slides/delete/{id}', [HomeController::class, 'deleteHomeSlide']);
            Route::post('/settings', [HomeController::class, 'update']);
            Route::post('/order/update/{id}', [HomeController::class, 'updateOrder']);
            Route::post('/toggle-status/{id}', [HomeController::class, 'toggleStatus']);
            Route::post('/free_shipping', [HomeController::class, 'updateFreeShipping']);

        });
    });
    
    Route::prefix('home_page')->middleware('language')->group(function () {
        // Public routes
        Route::get('/sections', [HomePageController::class, 'index']);
        Route::get('/section/{id}', [HomePageController::class, 'getSection']);
        Route::get('/banner', [HomePageController::class, 'getBanner']);
        Route::get('/item/{id}', [HomePageController::class, 'getItem']);
        Route::patch('/toggle-active', [HomePageController::class, 'toggleActive']);
    
        // Routes for super_admin or website_admin
        Route::middleware(['role:super_admin|website_admin'])->group(function () {
            // Home sections
            Route::post('/sections', [HomePageController::class, 'store']);
            Route::post('/sections/update/{id}', [HomePageController::class, 'update']);
            Route::delete('/sections/delete/{id}', [HomePageController::class, 'destroy']);
    
            // Home section items
            Route::post('/item', [HomePageController::class, 'createItem']);
            Route::post('/item/update/{id}', [HomePageController::class, 'updateItem']);
            Route::delete('/item/delete/{id}', [HomePageController::class, 'destroyItem']);
        });
    });
    
    Route::get('/DashboardAnalytics', [AnalyticsController::class, 'index']);
