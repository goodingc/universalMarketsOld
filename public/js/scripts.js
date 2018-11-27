class ProductSearch {
    constructor(params){
        var that = this;
        this.filterTypeSelect = params.filterTypeSelect;
        this.filterContent = params.filterContent;
        this.filterAdd = params.filterAdd;
        this.filterTypes = params.filterTypes;

        this.filters = [];

        for(var filterType in this.filterTypes){
            this.filterTypeSelect.append(new Option(this.filterTypes[filterType].name, filterType));
        }

        this.filterTypeSelect.on("change", function () {
            var id = that.filterContent.attr("id");
            that.filterContent.replaceWith(ProductSearch.inputType(that.filterTypes[$(this).val()].input));
            that.filterContent = $("#"+id);
        });

        this.filterAdd.on("click", function () {
            var filter = {
                id: (Math.random()*0xFFFFFF<<0).toString(16),
                type: that.filterTypeSelect.val(),
                content: that.filterContent.val(),
            };
            that.filters.push(filter);
            that.onFilterAdd(filter);
        });
    }

    addFilter(f){
        this.onFilterAdd = f;
        return this;
    }

    removeFilter(f){
        this.onFilterRemove = f;
        return this;
    }

    filterRemove(filterID){
        var that = this;
        this.filters.forEach(function (filter, index) {
            if(filterID == filter.id){
                that.filters.splice(index, 1);
                that.onFilterRemove();
            }
        })
    }

    getFilterName(filterType){
        return this.filterTypes[filterType].name;
    }

    static filterBadge(filterName, filterContent, filterID){
        return "<div class='btn-group mb-1 mr-1'><div class='btn btn-outline-primary disabled px-1 py-0'>"+
            filterName+": "+ filterContent+
            "</div><div id='filterRemove-"+filterID+"' class='btn btn-outline-danger px-1 py-0'>×</div></div>";
    }

    static inputType(params){
        switch (params.type) {
            case "text":
                return "<input class='form-control' id='filterContent' type='text'>";
            case "select":
                var output = "<select id='filterContent' class='custom-select'>";
                for(var option in params.options){
                    output += "<option value='"+option+"'>"+params.options[option]+"</option>";
                }
                output += "</select>";
                return output;
        }
        return "<div id='filterContent' class='form-control'>invalid input type!</div>";
    }
}
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

class Search {
    constructor(params){
        var that = this;
        this.filterTypeSelect = params.filterTypeSelect;
        this.filterContent = params.filterContent;
        this.filterAdd = params.filterAdd;
        this.filterTypes = params.filterTypes;

        this.filters = [];

        for(var filterType in this.filterTypes){
            this.filterTypeSelect.append(new Option(this.filterTypes[filterType].name, filterType));
        }

        this.filterTypeSelect.on("change", function () {
            var id = that.filterContent.attr("id");
            that.filterContent.replaceWith(ProductSearch.inputType(that.filterTypes[$(this).val()].input));
            that.filterContent = $("#"+id);
        });

        this.filterAdd.on("click", function () {
            var filter = {
                id: (Math.random()*0xFFFFFF<<0).toString(16),
                type: that.filterTypeSelect.val(),
                content: that.filterContent.val(),
            };
            that.filters.push(filter);
            that.onFilterAdd(filter);
        });
    }

    addFilter(f){
        this.onFilterAdd = f;
        return this;
    }

    removeFilter(f){
        this.onFilterRemove = f;
        return this;
    }

    filterRemove(filterID){
        var that = this;
        this.filters.forEach(function (filter, index) {
            if(filterID == filter.id){
                that.filters.splice(index, 1);
                that.onFilterRemove();
            }
        })
    }

    getFilterName(filterType){
        return this.filterTypes[filterType].name;
    }

    static filterBadge(filterName, filterContent, filterID){
        return "<div class='btn-group mb-1 mr-1'><div class='btn btn-outline-primary disabled px-1 py-0'>"+
            filterName+": "+ filterContent+
            "</div><div id='filterRemove-"+filterID+"' class='btn btn-outline-danger px-1 py-0'>×</div></div>";
    }

    static inputType(params){
        switch (params.type) {
            case "text":
                return "<input class='form-control' id='filterContent' type='text'>";
            case "select":
                var output = "<select id='filterContent' class='custom-select'>";
                for(var option in params.options){
                    output += "<option value='"+option+"'>"+params.options[option]+"</option>";
                }
                output += "</select>";
                return output;
            case "number":
                return "<input id='filterContent' type='number' class='form-control'>";
        }
        return "<div id='filterContent' class='form-control'>invalid input type!</div>";
    }
}
class Editor{
    constructor(params){
        this.trigger = params.trigger;
        this.input = params.input;
        this.editing = false;
        var that = this;

        this.trigger.on("click", function () {
            if(!that.editing){
                that.input.attr("disabled", false);
                that.trigger.html("Save");
                that.editing = true;
            }else{
                that.input.attr("disabled", true);
                that.trigger.html("Edit");
                that.editing = false;
                that.onTriggered(that.input.val());
            }
        })
    }

    triggered(f){
        this.onTriggered = f;
        return this;
    }
}
class Model {
    constructor(id, endpoint) {
        this.id = id;
        this.endpoint = endpoint;
    }

    edit(onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "POST",
            data: {
                api_token: apiToken,
                data: this,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        });
    }

    static create(endpoint, onDone){
        $.ajax({
            url: `/api/${endpoint}/create`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            onDone(data);
        })
    }

    destroy(onDone){
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "DELETE",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            onDone(data);
        })
    }
    
    populate(data, onDone){
        for (var property in data){
            this[property] = data[property];
        }
    }

}
class InventoryBay extends Model{
    constructor(id){
        super(id, "inventory-bays");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        super.get(function (data) {
            onDone(data);
        })
    }

    static show(onDone){
        $.ajax({
            url: `/api/product-attributes`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            var productAttributes = [];
            data.forEach(function (datum) {
                var productAttribute = new ProductAttribute(datum.id);
                productAttribute.data = datum;
                productAttributes.push(productAttribute);
            });
            onDone(productAttributes);
        })
    }

    static create(onDone){
        super.create("product-attributes", function (data) {
            var productAttribute = new ProductAttribute(data.id);
            productAttribute.data = data;
            onDone(productAttribute);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}
class Product extends Model{
    constructor(id){
        super(id, "products");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        super.get(function (data) {
            that.product_attributes.forEach(function (attributeData, key) {
                that.product_attributes[key] = new ProductAttribute(attributeData.id);
                that.product_attributes[key].populate(attributeData);
            });
            that.barcodes.forEach(function (barcodeData, key) {
                that.barcodes[key] = new ProductBarcode(barcodeData.id);
                that.barcodes[key].populate(barcodeData);
            });
            onDone(data);
        })
    }

    static create(onDone){
        super.create("products", function (data) {
            var product = new Product(data.id);
            product.populate(data);
            onDone(product);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }

    addAttribute(data, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/add`,
            method: "POST",
            data: {
                api_token: apiToken,
                data: data,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    removeAttribute(id, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/${id}`,
            method: "DELETE",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    editAttribute(id, value, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/${id}`,
            method: "POST",
            data: {
                api_token: apiToken,
                data:{
                    value: value
                }
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    addBarcode(){

    }
}
class ProductAttribute extends Model{
    constructor(id){
        super(id, "product-attributes");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        super.get(function (data) {
            onDone(data);
        })
    }

    static show(onDone){
        $.ajax({
            url: `/api/product-attributes`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            var productAttributes = [];
            data.forEach(function (datum) {
                var productAttribute = new ProductAttribute(datum.id);
                productAttribute.populate(datum);
                productAttributes.push(productAttribute);
            });
            onDone(productAttributes);
        })
    }

    static create(onDone){
        super.create("product-attributes", function (data) {
            var productAttribute = new ProductAttribute(data.id);
            productAttribute.populate(datum);
            onDone(productAttribute);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}
class ProductBarcode extends Model{
    constructor(id){
        super(id, "product-barcodes");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        super.get(function (data) {
            onDone(data);
        })
    }

    static show(onDone){
        $.ajax({
            url: `/api/product-barcodes`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            var productBarcodes = [];
            data.forEach(function (datum) {
                var productBarcode = new ProductBarcode(datum.id);
                productBarcode.populate(datum);
                productBarcodes.push(barcode);
            });
            onDone(productBarcodes);
        })
    }

    static create(onDone){
        super.create("product-barcodes", function (data) {
            var productBarcode = new ProductBarcode(data.id);
            productBarcode.populate(datum);
            onDone(barcode);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}
class ProductRange extends Model{
    constructor(id){
        super(id, "product-ranges");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        super.get(function (data) {
            that.products.forEach(function (productData, key) {
                that.products[key] = new Product(productData.id);
                that.products[key].populate(productData);
            })
            onDone(data);
        })
    }

    static create(onDone){
        super.create("product-ranges", function (data) {
            var productRange = new ProductRange(data.id);
            productRange.populate(data);
            onDone(productRange);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}