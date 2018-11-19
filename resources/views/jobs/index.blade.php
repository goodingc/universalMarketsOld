@extends("layouts.app")

@section("head")
    <script>
        var jobs = [];
        $(function () {
            $.ajax({
                url: "{{route("jobs.show")}}",
                data: {
                    api_token: apiToken,
                }
            }).done(function (jobs) {
                jobs.sort(function (a, b) {
                    if (a.id < b.id) return 1;
                    if (a.id > b.id) return -1;
                    return 0;
                });
                jobs.forEach(function (job) {
                    $("#jobList").append(`
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-3">
                                    <div class="row">Status ID: ${job.id}</div>
                                    <div class="row">Type: ${job.type.split("\\")[2]}</div>
                                    <div class="row">Job ID: ${job.job_id}</div>
                                    <div class="row">Created at: ${job.created_at}</div>
                                </div>
                                <div class="col-9">
                                    <div class="progress my-4">
                                        <div id="progress-${job.id}" class="progress-bar bg-info" role="progressbar" style="width: 0" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    `);
                    jobs.push(Job.make(job).progressed(function (state) {
                        var classes = "progress-bar ";
                        var width = 100;
                        var text = state.status;
                        switch(state.status){
                            case "queued":
                                classes+="bg-secondary progress-bar-striped progress-bar-animated";
                                break;
                            case "executing":
                                width = state.progress;
                                text = width+"%";
                                break;
                            case "finished":
                                classes+="bg-success";
                                break;
                            case "failed":
                                classes+="bg-danger";
                        }
                        var bar = $("#progress-"+job.id);
                        bar.width(width+"%");
                        bar.attr("class", classes);
                        bar.html(text);
                    }).progress());
                })
            })
        })
    </script>
@endsection

@section("content")
    <div class="container">
        @component("components.heroCard", ["title"=>"Jobs"])
            <ul id="jobList" class="list-group">

            </ul>
        @endcomponent
    </div>
@endsection