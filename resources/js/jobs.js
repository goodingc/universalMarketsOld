class JobFileUploader {
    constructor(options){
        this.fileSelect = options.fileSelect;
        this.statusOutput = options.statusOutput;
        this.uploadTrigger = options.uploadTrigger;
        this.target = options.target;
        this.onUpload = options.onUpload;
        this.formData = new FormData();
        this.fileSelect.on('change', {jobFileUploader: this}, function (event) {
            var input = $(this);
            var numFiles = input.get(0).files ? input.get(0).files.length : 1;
            var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            var output = event.data.jobFileUploader.statusOutput;
            var log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (output.length) {
                output.val(log);
            } else {
                if (log) alert(log);
            }
            Array.from(input.get(0).files).forEach(function (file) {
                event.data.jobFileUploader.formData.append("jobFiles[]", file, file.name);
            });
        });

        this.uploadTrigger.on("click", {jobFileUploader: this}, function (event) {
            $.ajax({
                type: "POST",
                url: event.data.jobFileUploader.target,
                data: event.data.jobFileUploader.formData,
                processData: false,
                contentType: false,

            }).done(function (data) {
                event.data.jobFileUploader.onUpload(data);
            });
        });
    }
}

class Job{
    constructor(options){
        this.operation = options.operation;
        this.data = options.data;
        this.saveTo = options.saveTo;
        this.onSave = options.onSave;
    }

    output(f){
        this.onOutput = f;
    }

    save(){
        var job = this;
        $.ajax({
            method: "POST",
            url: this.saveTo,
            data: {
                operation: this.operation,
                data: this.data,
            }
        }).done(function (data) {
            job.id = data.id;
            job.onSave(job);
        });
    }
}