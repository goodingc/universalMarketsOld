@extends('layouts.app')

@section("head")
    <script src="{{ asset('js/jobs.js') }}"></script>
    <script defer>
        var formData = new FormData();
        $(function () {
            jobfileUploader = new JobFileUploader({
                fileSelect: $("#jobFiles"),
                statusOutput: $("#jobFilesStatus"),
                uploadTrigger: $("#jobFilesUpload"),
                target: "{{route("jobs.upload", ["api_token"=>Auth::user()->api_token])}}",
                onUpload: function (files) {
                    console.log(files);
                }
            });

            testJob = new Job({
                operation: "StockLevelFileUpdate",
                data: {
                    test: "hi",
                },
                saveTo: "{{route("jobs.create", ["api_token"=>Auth::user()->api_token])}}",
                onSave: function (job) {
                    console.log(job);
                }
            }).save();
        })
    </script>
@endsection

@section("content")
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-hero shadow">
                <div class="card-header shadow">Jobs</div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <div class="input-group">
                                <label class="input-group-prepend">
                                    <span class="btn btn-secondary">
                                        Choose File <input id="jobFiles" style="display: none" type="file" multiple accept=".csv">
                                    </span>
                                </label>
                                <input id="jobFilesStatus" type="text" class="form-control" readonly>
                                <div class="input-group-append">
                                    <button id="jobFilesUpload" class="btn btn-primary form-control">
                                        Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="input-group">
                                <label class="input-group-prepend">
                                    <span class="btn btn-secondary">
                                        Choose File <input id="jobFiles" style="display: none" type="file" multiple accept=".csv">
                                    </span>
                                </label>
                                <input id="jobFilesStatus" type="text" class="form-control" readonly>
                                <div class="input-group-append">
                                    <button id="jobFilesUpload" class="btn btn-primary form-control">
                                        Upload
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card card-hero shadow">
                <div class="card-header shadow">Output</div>
                <div id="output" class="card-body" style="overflow-y: auto">
                </div>
            </div>
        </div>
    </div>
@endsection