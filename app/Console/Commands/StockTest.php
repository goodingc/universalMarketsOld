<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StockTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $data = DB::select(DB::raw("
        SELECT
            psc.product_id, p.sku, p.title, pb.barcode, tr.tax_rate, COUNT(bp.reason_id) > 0 AS 'blocked'
        FROM
            (tbl_product_sales_channels_distinct AS psc
                JOIN
            tbl_products AS p ON psc.product_id = p.id AND psc.sales_channel_id = ?
                LEFT JOIN
            tbl_product_barcodes AS pb ON p.id = pb.product_id and pb.quantity = 1
                JOIN
            tbl_tax_rates AS tr ON p.tax_rate_id = tr.id
                LEFT JOIN
            tbl_blocked_products AS bp ON bp.product_id = p.id AND (bp.sales_channel_id = psc.sales_channel_id OR bp.sales_channel_id = 0))
                GROUP BY
            p.id, pb.barcode;
       "),[18]);

        var_dump($data);
    }
}
