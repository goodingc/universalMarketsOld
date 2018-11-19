class JobFileUploader {
    constructor(options){
        this.fileInput = options.fileInput;
        this.statusOutput = options.statusOutput;
        this.formData = new FormData();
        this.formData.append("location", options.fileLocation);
        var that = this;
        this.fileInput.on('change', function (event) {
            var input = $(this);
            var numFiles = input.get(0).files ? input.get(0).files.length : 1;
            var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            var output = that.statusOutput;
            var log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (output.length) {
                output.val(log);
            } else {
                if (log) alert(log);
            }
            Array.from(input.get(0).files).forEach(function (file) {
                that.formData.append("jobFiles[]", file, file.name);
            });
            that.onSelect();
        });
    }

    upload(){
        var that = this;
        $.ajax({
            type: "POST",
            url: "/api/jobs/upload?api_token="+apiToken,
            data: this.formData,
            processData: false,
            contentType: false,
        }).done(function (data) {
            that.files = data;
            that.onUpload(data);
        });
    }

    selected(f){
        this.onSelect = f;
        return this;
    }

    done(f){
        this.onUpload = f;
        return this;
    }

}