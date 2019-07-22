<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Product extends Model {
    protected $table = "tbl_products";

    protected $guarded = ['id'];

    protected $casts = ["large_letter_compatible" => "boolean"];

    public function range() {
        return $this->belongsTo("App\Models\ProductRange", "product_range_id", "id");
    }

    public function attributeAssignments() {
        return $this->hasMany(ProductAttributeAssignment::class);
    }

    public function suppliers() {
        return $this->belongsToMany("App\Models\Supplier", "tbl_product_suppliers")->withPivot(Schema::getColumnListing("tbl_product_suppliers"));
    }

    public function salesChannelAssignments() {
        return $this->hasMany(SalesChannelAssignment::class);
    }

    public function inventoryBayAssignments(){
        return $this->hasMany("App\Models\InventoryBayAssignment");
        //return $this->belongsToMany("App\Models\InventoryBay", "tbl_product_inventory_bays")->withPivot(Schema::getColumnListing("tbl_product_inventory_bays"));
    }

    public function blocks(){
        return $this->hasMany("App\Models\ProductBlock");
        //return $this->belongsToMany("App\Models\ProductBlockReason", "tbl_blocked_products", "product_id", "reason_id")->withPivot(Schema::getColumnListing("tbl_blocked_products"));
    }

    public function barcodes(){
        return $this->hasMany("App\Models\ProductBarcode");
    }

    public function taxRate(){
        return $this->belongsTo("App\Models\TaxRate");
    }

    public function childGroups() {
        return $this->hasMany("App\Models\ProductGroup", "parent_id");
    }

    public function attribute(string $key){
        $attrID = ProductAttribute::where(["title"=>$key])->first()->id;
        if($entry = $this->attributeAssignments->where("product_attribute_id", "=" , $attrID)->first()){
            return $entry->value;
        }
        return false;
    }

    public function supplierStock(){
        $amazonDiscontinued = $this->attribute("AmazonDiscontinued");
        if($amazonDiscontinued && $amazonDiscontinued != "No"){
            return 0;
        }
        $stockLevel = 0;
        foreach ($this->suppliers as $supplier){
            $supplierLevel = $supplier->pivot->supplier_stock_level;
            if(!is_numeric($supplierLevel)) {
                switch ($supplierLevel){
                    case "A":
                    case "B":
                    case "C":
                    case "D":
                        $stockLevel += 10;
                        break;
                    default:
                        $stockLevel += 0;
                }
            }else{
                $stockLevel += $supplierLevel;
            }
        }
        return $stockLevel;
    }

    public function stockOnHand(){
        $amazonDiscontinued = $this->attribute("AmazonDiscontinued");
        if($amazonDiscontinued && $amazonDiscontinued != "No"){
            return 0;
        }
        $stockLevel = 0;
        foreach ($this->inventoryBayAssignments as $assignment) {
            $stockLevel += $assignment->quantity;
        }
        return $stockLevel;
    }

    public function costPrice($compFunction){
        $costPrice = -1;
        foreach ($this->suppliers as $supplier){
            if($costPrice == -1){
                $costPrice = $supplier->pivot->cost_price;
            }else{
                $costPrice = call_user_func($compFunction, $supplier->pivot->cost_price, $costPrice);
            }
        }
        return $costPrice;
    }

    public function getBlocksForSalesChannel(SalesChannel $salesChannel){
        return $this->blocks->filter(function (ProductBlock $block) use ($salesChannel){
            return $block->channel_id == 0 || $block->channel_id == $salesChannel->id;
        });
    }


}