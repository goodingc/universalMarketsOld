<?php

namespace App\Http\Controllers\API;

use App\Utils\Feed;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedController extends Controller {
    public function inventory(Request $request) {
        return nl2br(Feed::inventory($request->get("vendor"), $request->get("count")));
    }
}
