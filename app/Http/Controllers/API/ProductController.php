<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductRange;
use App\Utils\Search;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller {
    public function search(Request $request){
        $search = new Search([
            [
                "model" => Product::class,
                "map" => [
                    "sku" => "sku",
                    "title" => "title",
                ],
            ],
        ]);
        return $search->searchWith($request->get("filters"));
    }

    public function get(Request $request, int $id){
        $product = Product::find($id);
        $product->stockOnHand = $product->stockOnHand();
        $product->supplierStock = $product->supplierStock();
        $product->taxRate;
        $product->productAttributes;
        $product->suppliers;
        $product->salesChannels;
        $product->inventoryBays;
        $product->barcodes;
        return $product;
    }

    public function edit(Request $request, int $id){
        $product = Product::find($id);
        $data = $request->get("data");
        $attributes = $product->attributesToArray();
        foreach ($attributes as $attr => $value) {
            $product->{$attr} = $data[$attr];
        }
        $product->save();
        return $this->get($request, $id);
    }

    public function create(Request $request){
        $product = Product::create();
        return $product;
    }

    public function destroy(Request $request, int $id){
        Product::destroy($id);
    }

    public function addAttribute(Request $request, int $id){
        $product = Product::find($id);
        $data = $request->get("data");
        $product->productAttributes()->attach($data["product_attribute_id"], ["value" => $data["value"]]);
        $product->save();
        return $this->get($request, $id);
    }

    public function removeAttribute(Request $request, int $id, int $attrID){
        $product = Product::find($id);
        $product->productAttributes()->detach($attrID);
        $product->save();
        return $this->get($request, $id);
    }

}
