class StockFileUploader{
    constructor(options){
        this.fileInput = options.fileInput;
        this.statusOutput = options.statusOutput;
        this.uploadTrigger = options.uploadTrigger;
        this.fileSelect = options.fileSelect;
        this.headerTable = options.headerTable;
        this.progressList = options.progressList;
        this.files = [];
        this.jobs = [];
        this.stockLevelFileOptions = [];
        var that = this;
        this.jobFileUploader = new JobFileUploader({
            fileInput: this.fileInput,
            statusOutput: this.statusOutput,
            fileLocation: "stockFiles",
        }).selected(function () {
            var fileIndex = 0;
            var reader = new FileReader();
            reader.onload = function (event) {
                var contents = event.target.result;
                that.files.push({
                    name: that.fileInput.get(0).files[fileIndex].name,
                    headers: contents.substr(0, contents.indexOf('\n')).split(","),
                });
                if (that.fileInput.get(0).files[++fileIndex]) {
                    reader.readAsText(that.fileInput.get(0).files[fileIndex]);
                } else {
                    that.files.forEach(function (file) {
                        var option = new Option(file.name);
                        option.setAttribute("style", "color: #761b18; background-color: #f9d6d5;");
                        that.stockLevelFileOptions.push(option);
                        that.fileSelect.append(option);
                    });
                    that.fileSelect.parent().show();
                    that.headerTable.parent().show();
                    that.uploadTrigger.parent().show();
                    that.setHeaderTable(0);
                }
            };
            reader.readAsText(this.fileInput.get(0).files[fileIndex]);
        }).done(function (data) {

            that.files.forEach(function (file, index) {
                var job = new Job({
                    job: "UpdateStockLevels",
                    params: {
                        fileLoc: data[file.name],
                        sku: file.sku,
                        quantity: file.quantity,
                    }
                }).created(function () {
                    that.progressList.append(`
                        <li class="list-group-item" >
                            <div class="progress">
                                <div id="progress-${index}" class="progress-bar" style="width: 0;">${file.name}</div>
                            </div>
                        </li>
                    `);
                    job.progress();
                }).progressed(function (state) {
                    var classes = "progress-bar ";
                    var width = 100;
                    switch(state.status){
                        case "queued":
                            classes+="bg-secondary progress-bar-striped progress-bar-animated";
                            break;
                        case "executing":
                            width = state.progress;
                            break;
                        case "finished":
                            classes+="bg-success";
                            break;
                        case "failed":
                            classes+="bg-danger";
                    }
                    var bar = $("#progress-"+index);
                    bar.width(width+"%");
                    bar.attr("class", classes);
                    that.onProgress();
                }).create();
                that.jobs.push(job);
            })
        });

        this.fileSelect.on("change", function () {
            that.setHeaderTable($(this).prop("selectedIndex"));
        });


    }

    setHeaderTable(fileIndex){
        var table = this.headerTable.find("tbody");
        table.children().remove();
        var skuIndex = this.files[fileIndex].sku;
        var qtyIndex = this.files[fileIndex].quantity;
        var that = this;
        this.files[fileIndex].headers.forEach(function (header, index) {
            table.append(`
                <tr>
                    <td>${index}</td>
                    <td>${header}</td>
                    <td>
                        <input id='${fileIndex}-${index}-sku' type='radio' name="sku" class='form-check-input mx-auto' ${index == skuIndex ? "checked" : (index == qtyIndex ? "disabled" : "")}>
                    </td>
                    <td>
                        <input id='${fileIndex}-${index}-qty' type='radio' name="qty" class='form-check-input mx-auto' ${index == skuIndex ? "disabled" : (index == qtyIndex ? "checked" : "")}>
                    </td>
                </tr>
            `);
        });
        $("[id$='qty'], [id$='sku']").on("click", function () {
            var ref = $(this).attr("id").split("-");
            if (ref[2] == "sku") {
                that.files[ref[0]].sku = ref[1];
                $("[id$='qty']").attr("disabled", false);
                $("#" + ref[0] + "-" + ref[1] + "-qty").attr("disabled", true);
            } else {
                that.files[ref[0]].quantity = ref[1];
                $("[id$='sku']").attr("disabled", false);
                $("#" + ref[0] + "-" + ref[1] + "-sku").attr("disabled", true);
            }
            that.checkValid();
        });
    }

    checkValid(){
        var allValid = true;
        var that = this;
        this.files.forEach(function (file, index) {
            if (file.sku && file.quantity) {
                that.stockLevelFileOptions[index].setAttribute("style", "color: #1d643b; background-color: #d7f3e3;");
            } else {
                that.stockLevelFileOptions[index].setAttribute("style", "color: #761b18; background-color: #f9d6d5;");
                allValid = false;
            }
        });
        if (allValid) {
            this.uploadTrigger.attr("disabled", false);
        } else {
            this.uploadTrigger.attr("disabled", true);
        }
    }

    upload(){
        this.jobFileUploader.upload();
    }

    progressed(f){
        this.onProgress = f;
        return this;
    }

}