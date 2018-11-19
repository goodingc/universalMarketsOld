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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['sessions']], function () {
    Route::get("/suppliers/updateStockLevels", "API\SupplierDataController@updateStockLevels")->middleware("auth:api")->name("suppliers.updateStockLevels");
    Route::post("/suppliers/uploadStockFiles", "API\SupplierDataController@uploadStockFiles")->middleware("auth:api")->name("suppliers.uploadStockFiles");
    Route::get("/orders/get", "API\OrderController@get")->middleware("auth:api")->name("orders.get");
    Route::get("/recommendations/list", "API\RecommendationController@list")->middleware("auth:api")->name("recommendations.list");
    Route::get("/fulfillmentOrders/list", "API\FulfillmentInventoryController@list")->middleware("auth:api")->name("fulfillmentOrders.list");
    Route::get("/feed/inventory", "API\FeedController@inventory")->middleware("auth:api")->name("feed.inventory");
});



Route::namespace("API")->middleware("auth:api")->group(function (){
    Route::prefix("mws")->name("mws.")->namespace("MWS")->group(function (){
        set_time_limit(0);
        Route::prefix("orders")->name("orders")->group(function (){
            Route::get("", "OrderController@list")->name("");
            Route::get("{id}", "OrderController@get")->name("get");
        });

        Route::prefix("products")->name("products.")->group(function (){
            Route::get("search", "ProductController@search")->name("search");
            Route::get("feesEstimate/{asin}", "ProductController@feesEstimate")->name("feesEstimate");
            Route::get("{idType}/{id}", "ProductController@get")->name("get");
        });

        Route::prefix("inventory")->name("inventory.")->group(function (){
            Route::get("", "InventoryController@list")->name("list");
        });

        Route::prefix("finances")->name("inventory.")->group(function (){
            Route::get("", "FinanceController@list")->name("list");
            Route::get("{id}", "FinanceController@get")->name("get");
        });
    });

    Route::prefix("product-ranges")->name("productRanges")->group(function (){
        Route::get("search", "ProductRangeController@search")->name(".search");
        Route::get("create", "ProductRangeController@create")->name(".create");
        Route::get("{id}", "ProductRangeController@get")->name(".get");
        Route::post("{id}", "ProductRangeController@edit")->name(".edit");
        Route::delete("{id}", "ProductRangeController@destroy")->name(".destroy");
    });

    Route::prefix("products")->name("products")->group(function (){
        Route::get("search", "ProductController@search")->name(".search");
        Route::get("create", "ProductController@create")->name(".create");
        Route::get("{id}", "ProductController@get")->name(".get");
        Route::post("{id}", "ProductController@edit")->name(".edit");
        Route::delete("{id}", "ProductController@destroy")->name(".destroy");
        Route::prefix("{id}/attributes")->name(".attributes")->group(function (){
            Route::post("add", "ProductController@addAttribute")->name(".add");
            Route::delete("{attrID}", "ProductController@removeAttribute")->name(".remove");
        });
    });

    Route::prefix("product-attributes")->name("productAttributes")->group(function (){
        Route::get("", "ProductAttributeController@show")->name(".show");
        Route::get("create", "ProductAttributeController@create")->name(".create");
    });

    Route::prefix("jobs")->name("jobs")->group(function (){
        Route::post("upload", "JobController@upload")->name(".upload");
        Route::post("create", "JobController@create")->name(".create");
        Route::get("{statusID}/progress", "JobController@progress")->name(".progress");
        Route::get("", "JobController@show")->name(".show");
        Route::get("{statusID}", "JobController@get")->name(".get");
    });
});

