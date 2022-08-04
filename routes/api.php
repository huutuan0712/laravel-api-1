<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/email/verify/{id}', [VerificationController::class,'verify'])->name('verification.verify');
Route::group(['middleware' => 'api','prefix' => 'auth'], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/change-pass', [AuthController::class, 'changePassWord']);  
    Route::post('/user-profile', [AuthController::class, 'updateInformation']);  
});
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/update-password', [AuthController::class, 'updatePassword']);  
// PRODUCT
Route::get('/products',[ProductController::class,'index']);
Route::post('/product',[ProductController::class,'create']);
Route::get('/product/get-category/{slug}',[ProductController::class,'getProductbyCategory']);
Route::get('/product/{id}',[ProductController::class,'edit']);
Route::post('/product/{id}',[ProductController::class,'update']);
Route::delete('/product/{id}',[ProductController::class,'destroy']);
Route::get('/product/search-product/{search}',[ProductController::class,'search']);
Route::get('/product-sort',[ProductController::class,'sortProduct']);
// CATEGORY
Route::get('/categorys',[CategoryController::class,'index']);
 Route::post('/category',[CategoryController::class,'create']);
 Route::get('/category/{id}',[CategoryController::class,'edit']);
 Route::post('/category/{id}',[CategoryController::class,'update']);
 Route::delete('/category/{id}',[CategoryController::class,'destroy']);
 Route::get('/category/{search}',[CategoryController::class,'search']);
  // CART
    Route::get('cart/get-user/{id}',[CartController::class,'viewCart']);
    Route::post('cart/add-cart',[CartController::class,'addProduct']); 
    Route::post('cart/delete-cart',[CartController::class,'deleteProduct']);
    Route::post('cart/update-cart',[CartController::class,'updateProduct']);
    Route::get('cart/cart-preview/{id}',[CartController::class,'cartCount']);
    Route::post('cart/cart-placeorder',[CartController::class,'placeorder']);
    Route::get('cart/total-cart/{id}',[CartController::class,'totalCart']);
  

// ORDER
Route::get('admin/cart-order',[OrderController::class,'index']);
Route::post('admin/cart-status',[OrderController::class,'changeStatusAdmin']);
Route::post('cart/my-order',[OrderController::class,'myOrder']);
Route::post('cart/placeorder',[CheckoutController::class,'placeorder']);
Route::post('momo-payment/{id}',[CheckoutController::class,'momo_payment']);
// PROVINCE
Route::get('province',[CheckoutController::class,'province']);
Route::get('ward/{id}',[CheckoutController::class,'ward']);
Route::get('district/{id}',[CheckoutController::class,'district']);