<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FulfillmentInventoryController extends Controller {

    public function list(){
        try {
            $amz = new \AmazonFulfillmentOrderList("Bannan Tools", null, null, __DIR__ . "/../../../../config/amazon-config.php");
            $amz->fetchOrderList();
            return $amz->getFullList();
        } catch (\Exception $ex) {
            return 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
