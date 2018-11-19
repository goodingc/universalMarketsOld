class Job{
    constructor(options){
        this.job = options.job;
        this.params = options.params;
    }

    static make(params){
        var job = new Job({
            job: params.type.split("\\")[2],
            params: params.input
        });
        job.statusID = params.id;
        job.jobID = params.job_id;
        return job;
    }

    create(){
        var that = this;
        $.ajax({
            url: "/api/jobs/create?api_token="+apiToken,
            method: "POST",
            data: {
                job: this.job,
                params: this.params
            }
        }).done(function (data) {
            that.jobID = data.jobID;
            that.statusID = data.jobStatusID;
            that.onCreate();
        });
        return this;
    }

    progress(){
        var that = this;
        this.progressStream = $.SSE(`/api/jobs/${this.statusID}/progress?api_token=`+apiToken,{
            onMessage: function(e){
                //console.log(e);
            },
            events: {
                endStream: function () {
                    that.progressStream.stop();
                },
                state: function (data) {
                    data = JSON.parse(data.data);
                    that.state = data;
                    that.onProgress(data);
                }

            }
        });
        this.progressStream.start();
        return this;
    }

    progressed(f){
        this.onProgress = f;
        return this;
    }

    created(f){
        this.onCreate = f;
        return this;
    }
}
