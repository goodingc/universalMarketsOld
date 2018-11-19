<?php

namespace App\Http\Controllers\API\MWS;

use AmazonFinancialEventList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FinanceController extends Controller {
    public function list(Request $request) {
        $amz = new AmazonFinancialEventList("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setTimeLimits("-4month");
        $amz->fetchEventList();
        return $amz->getEvents();
    }

    public function get(Request $request, string $id) {
        $amz = new AmazonFinancialEventList("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setOrderFilter($id);
        $amz->fetchEventList();
        return $amz->getEvents();
    }
}
