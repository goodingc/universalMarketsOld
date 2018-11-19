<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Imtigger\LaravelJobStatus\Trackable;

class UpdateStockLevels implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    var $skuCol;
    var $qtyCol;
    var $fileLoc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $params) {
        $this->skuCol = $params["sku"];
        $this->qtyCol = $params["quantity"];
        $this->fileLoc = $params["fileLoc"];
        $this->prepareStatus();
        $this->setInput($params);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        Log::channel("daily")->info("Reading file {$this->fileLoc}");
        $csv = array_map('str_getcsv', explode("\n", Storage::get($this->fileLoc)));
        Log::channel("daily")->info("SKU header: {$csv[0][$this->skuCol]}");
        Log::channel("daily")->info("Quantity header: {$csv[0][$this->qtyCol]}");
        array_shift($csv);
        Log::channel("daily")->info(sizeof($csv)." items to update");
        $this->setProgressMax(sizeof($csv));
        foreach ($csv as $item) {
            try{
                if($productSupplier = DB::table("tbl_product_suppliers")->where("supplier_sku", "=", $item[$this->skuCol])->first()){
                    DB::table("tbl_product_suppliers")->where("supplier_sku", "=", $item[$this->skuCol])->update(["supplier_stock_level"=>max(0, $item[$this->qtyCol])]);
                }else{
                    Log::channel("daily")->error("Product {$item[$this->skuCol]} not found");
                }
            }catch (\Exception $e){
                Log::channel("daily")->error("Product {$item[$this->skuCol]} error: ".$e->getMessage());
            }

            $this->incrementProgress();
        }
    }
}
