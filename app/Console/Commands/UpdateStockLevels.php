<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Imtigger\LaravelJobStatus\JobStatus;

class UpdateStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:update {fileLoc} {skuCol} {qtyCol}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update stock level data from file';

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
        $fileLoc = $this->argument("fileLoc");
        $skuCol = $this->argument("skuCol");
        $qtyCol = $this->argument("qtyCol");
        $this->info("Creating stock update job on {$fileLoc}, SKU:{$skuCol}, quantity:{$qtyCol}");
        \App\Jobs\UpdateStockLevels::dispatch([
            "sku"=>$skuCol,
            "quantity"=>$qtyCol,
            "fileLoc"=>$fileLoc,
        ])->onConnection("database");
    }
}
