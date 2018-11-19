<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecommendationController extends Controller {
    public function list(){
        try {
            $amz = new \AmazonRecommendationList("Bannan Tools", null, null, __DIR__ . "/../../../../config/amazon-config.php");
            $amz->fetchRecommendations();
            return $amz->getLists();
        } catch (\Exception $ex) {
            return 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }
    }
}
