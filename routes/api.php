<?php

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

Route::middleware('auth:api')->group(function() {
    Route::middleware('clear.cache:api')->group(function() {
        Route::post('event', 'EventController@create');
        Route::post('payment', 'PaymentController@create');
    });
    Route::middleware('cache:api')->group(function() {
        Route::get('charges', 'ChargeController@getAll');
        Route::get('charges/{chargeId}', 'ChargeController@get');
        Route::get('events', 'EventController@getAll');
        Route::get('events/{eventId}', 'EventController@get');
        Route::get('invoices', 'InvoiceController@getAll');
        Route::get('invoices/{invoiceId}', 'InvoiceController@get');
        Route::get('payments', 'PaymentController@getAll');
        Route::get('payments/{paymentId}', 'PaymentController@get');
        Route::get('users/status', 'UserFinantialStatusController@getAll');
        Route::get('users/{userId}/status', 'UserFinantialStatusController@getByUserId');
        Route::get('users/status/{statusId}', 'UserFinantialStatusController@get');
    });
});
