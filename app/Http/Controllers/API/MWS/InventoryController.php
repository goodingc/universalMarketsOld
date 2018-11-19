<?php

namespace App\Http\Controllers\API\MWS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller {
    public function list(Request $request) {
        $amz = new \AmazonInventoryList("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setStartTime("-5year");
        $amz->fetchInventoryList();
        $list = $amz->getSupply();
        return $list;
    }
}
