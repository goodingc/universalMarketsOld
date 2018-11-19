<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\SalesChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\Trackable;

class StockFeed implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    var $vendorCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params) {
        $this->vendorCode = $params["vendorCode"];
        $this->prepareStatus();
        $this->setInput($params);
    }

    public function handle() {
        $feedId = "RETAIL_FEED_{$this->vendorCode}_".date("Ymd")."_00.TXT";
        Log::channel("daily")->info("Starting StockFeed Job with output {$feedId}");
        $this->setOutput(["stockFeedFile" => $feedId]);
        $products = DB::select(DB::raw(/** @lang SQL */"CALL STOCK_FEED(?);"),[$this->vendorCode]);
        $this->setProgressMax(sizeof($products));
        Storage::disk("bannanTools")->put("stockFeeds/".$feedId,"ISBN|EAN|UPC|VENDOR_STOCK_ID|TITLE|QTY_ON_HAND|LIST_PRICE_EXCL_TAX|LIST_PRICE_INCL_TAX|COST_PRICE|DISCOUNT|ISO_CURRENCY_CODE");
        Storage::disk("local")->put("stockFeeds/".$feedId,"ISBN|EAN|UPC|VENDOR_STOCK_ID|TITLE|QTY_ON_HAND|LIST_PRICE_EXCL_TAX|LIST_PRICE_INCL_TAX|COST_PRICE|DISCOUNT|ISO_CURRENCY_CODE");
        $flushMax = 100;
        $flush = 0;
        $buffer = "";
        foreach ($products as $product) {
            $buffer.= "|{$product->barcode}||{$product->sku}|{$product->title}|{$product->stock_level}|||{$product->cost_price}||GBP\n";

            if(++$flush == $flushMax){
                Storage::disk("bannanTools")->append("stockFeeds/".$feedId, rtrim($buffer));
                Storage::disk("local")->append("stockFeeds/".$feedId, rtrim($buffer));
                $buffer = "";
                $flush = 0;
            }
            $this->incrementProgress();
        }
        Storage::disk($this->vendorCode."_up")->put($feedId, Storage::disk("local")->get("stockFeeds/".$feedId));
    }
}
