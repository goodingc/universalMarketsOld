<?php

namespace App\Console\Commands;

use App\Models\SalesChannel;
use App\Utils\Feed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class StockFeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:feed {vendorCode?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push stock data to Amazon';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if(is_null($vendorCode = $this->argument("vendorCode"))){
            $salesChannels = SalesChannel::all();
            foreach ($salesChannels as $salesChannel){
                if (!is_null($vendorCode = $salesChannel->parameter("VendorCode"))){
                    $this->info("Creating a job for {$vendorCode} INVRPT");
                    \App\Jobs\StockFeed::dispatch([
                        "vendorCode"=>$vendorCode,
                    ])->onConnection("database");
                }
            }
        }else{
            $this->info("Creating a job for {$vendorCode} INVRPT");
            \App\Jobs\StockFeed::dispatch([
                "vendorCode"=>$vendorCode,
            ])->onConnection("database");
        }
    }
}
