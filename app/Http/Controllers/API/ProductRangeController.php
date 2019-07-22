<?php

namespace App\Http\Controllers\API;

use App\Models\ProductRange;
use App\Utils\Search;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ProductRangeController extends ModelController {
    protected $modelClass = ProductRange::class;

    public function search(Request $request){
        $search = new Search([
            [
                "model" => ProductRange::class,
                "map" => [
                    "sku" => "sku",
                    "title" => "title",
                ]
            ]
        ]);
        return $search->searchWith($request->get("filters"));
    }

    public function populate($productRange) {
        $productRange->products;
    }


}
