<?php
/**
 * Created by PhpStorm.
 * User: callu
 * Date: 04/10/2018
 * Time: 01:49
 */

namespace App\Utils;
use App\Models\Product;
use App\Models\SalesChannel;

include __DIR__."/../../config/salesChannels.php";


class Feed {
    public static function inventory(string $vendorCode, int $limit = 0){
        $feed = "";
        error_log("entry");
        $salesChannels = SalesChannel::all();
        foreach ($salesChannels as $salesChannel){
            if ($salesChannel->parameter("VendorCode") == $vendorCode){
                $products = $salesChannel->products;
                $feed .= "ISBN|EAN|UPC|VENDOR_STOCK_ID|TITLE|QTY_ON_HAND|LIST_PRICE_EXCL_TAX|LIST_PRICE_INCL_TAX|COST_PRICE|DISCOUNT|ISO_CURRENCY_CODE\n";
                $stockFunc = $salesChannel->stock_calculation_method == "STOCK_ON_HAND"?"stockOnHand":"supplierStock";
                $count = 0;
                $vatCalc = function (Product $product) use ($salesChannel){
                    if($salesChannel->parameter("IncludeVatInOutputPrice") == "Yes"){
                        return ceil($product->pivot->sell_price_ex_vat * (100 +  $product->taxRate->tax_rate))/100;
                    }
                    return $product->pivot->sell_price_ex_vat;
                };
                foreach ($products as $product){
                    error_log($product->sku);
                    if($ean = $product->barcodes->where("quantity", "=", 1)->first()){
                        $ean = $ean->barcode;
                    }
                    $feed .= "|".($ean?:"")."||{$product->sku}|{$product->title}|".$product->$stockFunc()."|||{$vatCalc($product)}||GBP\n";
                    if(++$count == $limit){
                        return $feed;
                    }
                }
                return $feed;
            }
        }
        return "not found";
    }
}