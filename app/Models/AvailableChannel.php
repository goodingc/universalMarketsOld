<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AvailableChannel extends Model {
    protected $table = "tbl_available_channels";

    public function salesChannels(){
        return $this->hasMany("App\Models\SalesChannel");
    }
}
