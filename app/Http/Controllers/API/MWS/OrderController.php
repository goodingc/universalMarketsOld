<?php

namespace App\Http\Controllers\API\MWS;

use AmazonOrderList;
use AmazonOrderSet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller {
    public function list(Request $request) {
        $amz = new AmazonOrderList("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setUseToken();
        $amz->setLimits($request->get("limitFilter", "Created"), $request->get("limitLower", "-1year"),$request->get("limitUpper"));
        $amz->setOrderStatusFilter($request->get("orderStatus"));
        $amz->setFulfillmentChannelFilter($request->get("fulfillmentChannel"));
        $amz->setPaymentMethodFilter($request->get("paymentMethod"));
        $amz->setEmailFilter($request->get("email"));

        $amz->fetchOrders();
        $orders = $amz->getList();
        foreach ($orders as $index => $order){
            $items = $order->fetchItems()->getItems();
            $orders[$index] = $order->getData();
            $orders[$index]["Items"] = $items;
        }
        return $orders;
    }

    public function get(Request $request, string $id){
        $amz = new AmazonOrderSet("Bannan Tools", null, null, null, app_path()."/../config/amazon-config.php");
        $amz->setOrderIds($id);
        $amz->fetchOrders();
        $order = $amz->getOrders()[0];
        $items = $order->fetchItems()->getItems();
        $order = $order->getData();
        $order["Items"] = $items;
        return $order;
    }



}
