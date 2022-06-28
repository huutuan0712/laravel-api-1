<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerificationController;

use Illuminate\Http\Request;
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
// Route::get('/verified-only', function(Request $request){

//     dd('your are verified', $request->user()->name);
// })->middleware('auth:api','verified');
// Route::get('/email/resend', [VerifiedMailController::class,'resend'])->name('verification.resend');

Route::get('/email/verify/{id}', [VerificationController::class,'verify'])->name('verification.verify');
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/change-pass', [AuthController::class, 'changePassWord']);   
    
    
  
});