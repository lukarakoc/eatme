<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'HomeController@index');

Auth::routes();


Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    Route::get('/admin-info', 'HomeController@adminInfo');

    Route::get('/users', 'UserController@getUsers');
    Route::post('/users/store', 'UserController@store');
    Route::post('/users/update', 'UserController@update');
    Route::delete('/users/{id}', 'UserController@destroy');
    Route::get('/users/{keyword}/search', 'UserController@searchUsers');

    Route::get('/groceries', 'GroceryController@getGroceries');
    Route::post('/groceries/store', 'GroceryController@store');
    Route::post('/groceries/update', 'GroceryController@update');
    Route::delete('/groceries/{id}', 'GroceryController@destroy');
    Route::get('/groceries/{keyword}/search', 'GroceryController@searchGroceries');
    Route::get('/groceries/only', 'GroceryController@getOnlyAllGroceries');
    Route::get('/groceries/products', 'GroceryController@getOnlyAllProducts');

    Route::get('/purchases', 'PurchaseController@getPurchases');
    Route::delete('/purchases/{id}', 'PurchaseController@destroy');
    Route::post('/purchases/store', 'PurchaseController@storePurchase');
    Route::get('/purchases/{keyword}/search', 'PurchaseController@searchPurchases');

    Route::get('/sales', 'SaleController@getSales');
    Route::delete('/sales/{id}', 'SaleController@destroy');
    Route::post('/sales/store', 'SaleController@storeSale');
//    Route::get('/sales/{keyword}/search', 'SaleController@searchSales');

    Route::get('/expenses', 'ExpenseController@getExpenses');
    Route::delete('/expenses/{id}', 'ExpenseController@destroy');
    Route::post('/expenses/store', 'ExpenseController@storeExpense');
    Route::get('/expenses/{keyword}/search', 'ExpenseController@searchExpenses');
    Route::get('/expenses/types', 'ExpenseController@getAllTypes');

    Route::get('/roles', 'UserController@getAllRoles');
    Route::get('/clients', 'ClientController@getAllClients');
    Route::get('/dashboard', 'DashboardController@getDashboardData');

});
