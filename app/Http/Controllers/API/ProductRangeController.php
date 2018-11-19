<?php

namespace App\Http\Controllers\API;

use App\Models\ProductRange;
use App\Utils\Search;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ProductRangeController extends Controller {
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

    public function get(Request $request, int $id){
        $productRange = ProductRange::find($id);
        $productRange->products;
        return $productRange;
    }

    public function edit(Request $request, int $id){
        $productRange = ProductRange::find($id);
        $data = $request->get("data");
        $productRange->sku = $data["sku"];
        $productRange->title = $data["title"];
        $productRange->save();
        $productRange->products;
        return $productRange;
    }

    public function create(Request $request){
        $productRange = ProductRange::create();
        return $productRange;
    }

    public function destroy(Request $request, int $id){
        ProductRange::destroy($id);
    }


}
