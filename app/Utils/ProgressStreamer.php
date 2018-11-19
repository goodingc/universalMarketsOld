<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ProgressStreamer extends StreamedResponse{

    public function __construct() {
        parent::__construct();
        $this->headers->set('Content-Type', 'text/event-stream');
        $this->headers->set('X-Accel-Buffering', 'no');
        $this->headers->set('Cache-Control', 'no-cache');
    }

    public static function stream($event, $data = null){
        echo "retry: 0\n";
        echo "event: {$event}\n";
        echo "data: ".json_encode($data??"")."\n\n";
        ob_flush();
        flush();
    }

    public static function end(){
        echo "event: endStream\n";
        echo "data: end\n\n";
        ob_flush();
        flush();
    }



}