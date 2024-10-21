<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DescriptionController;
use App\Http\Controllers\Api\V1\DiscountController;
use App\Http\Controllers\Api\V1\FeatureController;
use App\Http\Controllers\Api\V1\GoogleAuthController;
use App\Http\Controllers\Api\V1\HistoryController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|------------
--------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['middleware' => 'api', 'prefix' => '/V1'], function($router) {
    Route::group(['prefix' => '/auth'], function ($router){
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
        Route::post('/verifyForgot', [AuthController::class, 'verifyForgot']);
        Route::post('/resetPassword', [AuthController::class, 'resetPassword']);
        Route::post('/resendCode', [AuthController::class, 'resendCode']);
        Route::post('/register', [AuthController::class, 'store']);
        Route::post('/userDetails', [AuthController::class, 'storeDetails']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::get('/getUser', [AuthController::class, 'getUser']);
        Route::post('/deleteUser', [AuthController::class, 'deleteUser']);
    });
    Route::post('/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);
    Route::get('/google', [GoogleAuthController::class, 'redirect']);
    Route::post('/contact', [ContactController::class, 'store']);
        Route::get('/job', [JobController::class, 'index']);
        Route::post('/job/subscribe', [JobController::class, 'subscribe']);
    Route::group(['middleware'=> 'auth'], function ($router) {
        Route::post('/editProfile', [AuthController::class, 'editProfile']);
        Route::post('/setNotification', [SettingController::class, 'setNotification']);
        Route::resource('description', DescriptionController::class);
        Route::resource('notification', NotificationController::class);
        Route::resource('setting', SettingController::class);
        Route::post('description/update/{id}', [DescriptionController::class, 'update']);
        Route::get('/getCart', [CheckoutController::class, 'returnCart']);
        Route::get('/handleCallback/{id}', [CheckoutController::class, 'handleCallback']);
        Route::delete('/deleteCartItem/{id}', [DescriptionController::class, 'destroyCart']);
        Route::post('/discount/apply', [DiscountController::class, 'applyCode']);
        Route::get('/getFeatures', [FeatureController::class, 'index']);
        Route::get('/getFeature/{name}', [FeatureController::class, 'show']);
        Route::get('/history', [HistoryController::class, 'history']);
        Route::get('/deliverReminder/{id}', [OrderController::class, 'deliverReminder']);
        Route::get('/markDelivered/{id}', [OrderController::class, 'markDelivered']);
        Route::get('/markRetrieved/{id}', [OrderController::class, 'markRetrieved']);
        Route::get('/completeOrder/{id}', [OrderController::class, 'completeOrder']);
        Route::get('/retrieveReminder/{id}', [OrderController::class, 'retrieveReminder']);
        Route::resource('cart', CartController::class);
        Route::resource('checkout', CheckoutController::class);
    });

    Route::group(['middleware'=> 'isAdmin', 'prefix' => '/admin'], function ($router) {
        Route::resource('feature', FeatureController::class);
        Route::resource('order', OrderController::class);
        Route::resource('discount', DiscountController::class);
        Route::resource('job', JobController::class);
        Route::post('feature/update/{id}', [FeatureController::class, 'edit']);
        Route::post('job/update/{id}', [JobController::class, 'update']);
        Route::resource('discount', DiscountController::class);
        Route::post('discount/update/{id}', [DiscountController::class, 'update']);
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::get('/allUsers', [AdminController::class, 'allUsers']);
        Route::get('/makeAdmin/{id}', [AdminController::class, 'makeAdmin']);
        Route::get('/unmakeAdmin/{id}', [AdminController::class, 'unmakeAdmin']);
        Route::get('users/{year}/{month}', [AdminController::class, 'users']);
    });
});
