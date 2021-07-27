<?php

use App\Http\Controllers\BooksController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\UserController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('login', [UserController::class, 'login']);
    Route::post('register', [UserController::class, 'register']);
    Route::post('signout', [UserController::class, 'signout']);
    Route::post('forgotPassword', [UserController::class, 'forgotPassword']);
    Route::post('resetPassword', [UserController::class, 'resetPassword']);

    Route::post('insertBook', [BooksController::class, 'addBook']);
    Route::get('displayBooks', [BooksController::class, 'displayBooks']);
    Route::post('addToCart', [BooksController::class, 'addToCart']);
    Route::get('displayBooksInCart', [BooksController::class, 'displayBooksInCart']);
    Route::post('removeFromCart', [BooksController::class, 'removeFromCart']);

    Route::post('customerRegister', [Customer::class, 'customerRegister']);
    Route::post('orderPlacedSuccessfull', [Customer::class, 'orderPlacedSuccessfull']);
});
