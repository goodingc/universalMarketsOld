<?php

namespace App\Http\Controllers\API\MWS;

use AmazonProductFeeEstimate;
use AmazonProductInfo;
use AmazonProductList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller {
    public function search(Request $request) {
        $amz = new \AmazonProductSearch("Bannan Tools", $request->get("query"), null, null, app_path()."/../config/amazon-config.php");
        $amz->setContextId($request->get("context"));
        $amz->searchProducts();
        $products = $amz->getProduct();
        foreach ($products as $index => $product) {
            $products[$index] = $product->getData();
        }
        return $products;
    }

    public function get(Request $request, string $idType, string $id){
        $amz = new AmazonProductList("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setIdType($idType);
        $amz->setProductIds($id);
        $amz->fetchProductList();
        $product = $amz->getProduct()[0]->getData();
        $amz = new AmazonProductInfo("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");
        $amz->setASINs($product["Identifiers"]["MarketplaceASIN"]["ASIN"]);
        $amz->fetchCompetitivePricing();
        $amz->fetchCategories();
        $amz->fetchLowestOffer();
        $amz->fetchMyPrice();
        $info = $amz->getProduct()[0]->getData();
        $product["Info"] = $info;
        return $product;
    }

    public function feesEstimate(Request $request, string $asin){
        $amz = new AmazonProductFeeEstimate("Bannan Tools", null, null, app_path()."/../config/amazon-config.php");

        $amz->setRequests([[
            "MarketplaceId" => "A1F83G8C2ARO7P",
            "IdType" => "ASIN",
            "IdValue" => $asin,
            "ListingPrice" => [
                "CurrencyCode" => "GBP",
                "Value" => $request->get("listPrice"),
            ],
            "Identifier" => time(),
            "IsAmazonFulfilled" => true
        ]]);
        $amz->fetchEstimates();
        return $amz->getEstimates();
    }
}
