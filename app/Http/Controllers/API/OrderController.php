<?php

namespace App\Http\Controllers\API;

use AmazonOrderList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller {
    public function get(){
        try {
            $amz = new AmazonOrderList("Bannan Tools", null, null, __DIR__."/../../../../config/amazon-config.php"); //store name matches the array key in the config file
            $amz->setLimits('Modified', "- 1 year"); //accepts either specific timestamps or relative times
            //$amz->setFulfillmentChannelFilter("MFN"); //no Amazon-fulfilled orders
            $amz->setUseToken(); //tells the object to automatically use tokens right away
            $amz->fetchOrders(); //this is what actually sends the request
            $list = $amz->getList();
            $outputList = [];
            foreach ($list as $order) {
                $outputOrder = new \stdClass();
                $outputOrder->id = $order->getAmazonOrderId();
                $outputOrder->purchaseDate = $order->getPurchaseDate();
                $outputOrder->lastUpdateDate = $order->getLastUpdateDate();
                $outputOrder->status = $order->getOrderStatus();
                $outputOrder->total = $order->getOrderTotal();
                $outputList[] = $outputOrder;
            }
            return json_encode($outputList);
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
