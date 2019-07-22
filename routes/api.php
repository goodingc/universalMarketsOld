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

    modelControllerRoutes("product-groups", "productRanges", "ProductRangeController", function ($controller){
        Route::get("search", $controller."@search")->name(".search");
    });
    modelControllerRoutes("products", "products", "ProductController", function ($controller){
        Route::get("search", $controller."@search")->name(".search");
    });
    modelControllerRoutes("product-attribute-assignments", "productAttributeAssignments", "ProductAttributeAssignmentController");
    modelControllerRoutes("inventory-bay/assignments", "inventoryBayAssignments", "InventoryBayAssignmentController");
    modelControllerRoutes("product-groups", "productGroups", "ProductGroupController");
    modelControllerRoutes("product-attributes", "productAttributes", "ProductAttributeController");
    modelControllerRoutes("inventory-bays", "inventoryBays", "InventoryBayController");
    modelControllerRoutes("product-barcodes", "productBarcodes", "ProductBarcodeController");
    modelControllerRoutes("product-blocks", "productBlocks", "ProductBlockController");
    modelControllerRoutes("product-block-reasons", "productBlockReasons", "ProductBlockReasonController");
    modelControllerRoutes("sales-channel-assignments", "salesChannelAssignments", "SalesChannelAssignmentController");

    Route::prefix("jobs")->name("jobs")->group(function (){
        Route::post("upload", "JobController@upload")->name(".upload");
        Route::post("create", "JobController@create")->name(".create");
        Route::get("{statusID}/progress", "JobController@progress")->name(".progress");
        Route::get("", "JobController@show")->name(".show");
        Route::get("{statusID}", "JobController@get")->name(".get");
    });
});

function modelControllerRoutes(string $prefix, string $name, string $controller, Closure $customRoutes = null){
    Route::prefix($prefix)->name($name)->group(function () use ($controller, $customRoutes){
        Route::get("show", $controller."@show")->name(".show");
        Route::post("create", $controller."@create")->name(".create");
        Route::get("", $controller."@get")->name(".get");
        Route::post("", $controller."@edit")->name(".edit");
        Route::delete("", $controller."@destroy")->name(".destroy");
        Route::get("attributes", $controller."@attributes")->name(".attributes");
        if($customRoutes != null) $customRoutes($controller);
    });
}