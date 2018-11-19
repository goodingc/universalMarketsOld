<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\Supplier;
use App\Utils\ProgressStreamer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;

class SupplierDataController extends Controller {
    public static function updateStockLevels(Request $request){

        $metadata = $request->session()->pull("stockLevelFilesMetadata");
        $response = new ProgressStreamer();
        $response->setCallback(function () use ($metadata){
            ProgressStreamer::stream("message", "Data from ".sizeof($metadata)." file(s)");
            foreach ($metadata as $metadatum) {
                ProgressStreamer::stream("message", "Reading file {$metadatum->name}@{$metadatum->tmpLoc}");
                $file = explode("\n", Storage::get($metadatum->tmpLoc));
                $csv = array_map('str_getcsv', $file);
                ProgressStreamer::stream("message", "SKU header: {$csv[0][$metadatum->sku]}");
                ProgressStreamer::stream("message", "Quantity header: {$csv[0][$metadatum->quantity]}");
                array_shift($csv);
                ProgressStreamer::stream("message", sizeof($csv)." items to update");
                foreach ($csv as $item) {
                    if($productSupplier = DB::table("tbl_product_suppliers")->where("supplier_sku", "=", $item[$metadatum->sku])->first()){
                        ProgressStreamer::stream("message", "Found product {$item[$metadatum->sku]}");
                        $oldLevel = $productSupplier->supplier_stock_level;
                        DB::table("tbl_product_suppliers")->where("supplier_sku", "=", $item[$metadatum->sku])->update(["supplier_stock_level"=>$item[$metadatum->quantity]]);
                        ProgressStreamer::stream("message", "Changed stock level from {$oldLevel} to {$item[$metadatum->quantity]}");
                    }else{
                        ProgressStreamer::stream("message", "Product {$item[$metadatum->sku]} not found");
                    }
                }
            }
            ProgressStreamer::end();
        });
        return $response;
    }
    
    public static function uploadStockFiles(Request $request){
        $metadata = json_decode($request->get("headerData"));
        foreach ($request->file("stockLevelFiles") as $index => $file) {
            $metadata[$index]->tmpLoc = $file->store("stockLevelFiles");
        }
        $request->session()->put("stockLevelFilesMetadata", $metadata);
    }
}
