<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->middleware("auth")->middleware("auth.api")->name('home');

Route::get("/tests", "TestController@index")->middleware("auth")->name("tests");

Route::prefix("product-ranges")->name("productRanges")->middleware("auth")->middleware("auth.api")->group(function (){
    Route::get("", "ProductRangeController@index")->name("");
    Route::get("{id}", "ProductRangeController@viewPreload")->name(".viewPreload");
    Route::get("{id}/view", "ProductRangeController@view")->name(".view");
});

Route::prefix("products")->name("products")->middleware("auth")->middleware("auth.api")->group(function (){
    Route::get("", "ProductController@index")->name("");
    Route::get("{id}", "ProductController@viewPreload")->name(".viewPreload");
    Route::get("{id}/view", "ProductController@view")->name(".view");
});

Route::prefix("product-attributes")->name("productAttributes")->middleware("auth")->middleware("auth.api")->group(function (){
    Route::get("", "ProductAttributeController@index")->name("");
    Route::get("{id}", "ProductAttributeController@viewPreload")->name(".viewPreload");
    Route::get("{id}/view", "ProductAttributeController@view")->name(".view");
});

Route::prefix("stock")->name("stock")->middleware(["auth", "auth.api"])->group(function (){
    Route::get("upload", "StockController@upload")->name(".upload");
});

Route::prefix("jobs")->name("jobs")->middleware(["auth", "auth.api"])->group(function (){
    Route::get("", "JobController@index")->name("");
});