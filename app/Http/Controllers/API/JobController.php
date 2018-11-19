<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\StockFeed;
use App\Utils\ProgressStreamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Imtigger\LaravelJobStatus\JobStatus;

class JobController extends Controller {
    public function create(Request $request){
        $className = "\App\Jobs\\".$request->get("job");
        $job = new $className($request->get("params"));
        $job->onConnection("database");
        $jobID = $this->dispatch($job);
        return ["jobID"=>$jobID, "jobStatusID"=>$job->getJobStatusId()];
    }

    public function upload(Request $request){
        $fileLocations = [];
        foreach ($request->file("jobFiles") as $index => $file) {
            $fileLocations[$file->getClientOriginalName()] = $file->store($request->get("location"));
        }
        return $fileLocations;
    }

    public function progress(int $statusID){
        $job = JobStatus::find($statusID);
        $response = new ProgressStreamer();
        $response->setCallback(function () use ($job){
            ProgressStreamer::stream("state", ["status" => $job->status, "progress" => $job->progress_percentage]);
            while(!$job->is_ended){
                $job = $job->fresh();
                ProgressStreamer::stream("state", ["status" => $job->status, "progress" => $job->progress_percentage]);
                sleep(1);
            }
            ProgressStreamer::end();
        });
        return $response;
    }

    public function show() {
        return JobStatus::all();
    }

    public function get(int $statusID){
        return JobStatus::find($statusID);
    }
}
