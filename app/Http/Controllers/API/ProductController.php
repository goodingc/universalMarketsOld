<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductBarcode;
use App\Models\ProductRange;
use App\Utils\ModelUtils;
use App\Utils\Search;
use EDI\Parser;
use Faker\Provider\Barcode;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends ModelController {
    protected $modelClass = Product::class;

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

    public function populate($product) {
        $product->stockOnHand = $product->stockOnHand();
        $product->supplierStock = $product->supplierStock();
        $product->taxRate;
        $product->attributeAssignments->each(function ($val){
            $val->attribute;
        });
        $product->suppliers;
        $product->salesChannelAssignments->each(function ($assignment){
            $assignment->salesChannel;
        });
        $product->inventoryBayAssignments->each(function ($assignment){
            $assignment->bay->warehouse;
        });
        $product->barcodes;
        $product->blocks->each(function ($block){
            $block->reason;
        });
        $product->childGroups->each(function ($group){
            $group->product;
        });
    }

}
