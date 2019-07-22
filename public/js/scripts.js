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
                content: "%"+that.filterContent.val()+"%",
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
var Model = function (attributes) {
    if(typeof attributes === "number"){
        this.id = attributes;
    }else{
        this.populate(attributes);
    }
};


Model.prototype.getAttributes = function(){
    let attributes =  JSON.parse(JSON.stringify(this));
    for (attribute in attributes){
        if(typeof attributes[attribute] === "object" ){
            delete attributes[attribute];
        }
    }
    return attributes;
};

Model.prototype.show = function (attributes = null, onDone) {
    $.ajax({
        url: `/api/${this.endpoint}/show`,
        method: "GET",
        data: {
            api_token: apiToken,
            attributes: attributes
        },
    }).done((data)=>{
        let models = [];
        data.forEach((datum)=>{
            models.push(new window[this.constructor.name](datum));
        });
        onDone(models);
    });
};

Model.prototype.create = function (attributes = null, onDone){
    $.ajax({
        url: `/api/${this.endpoint}/create`,
        method: "POST",
        data: {
            api_token: apiToken,
            attributes: attributes,
        }
    }).done((data) => {
        onDone(new window[this.constructor.name](data));
    });
};

Model.prototype.get = function(onDone){
    $.ajax({
        url: `/api/${this.endpoint}`,
        method: "GET",
        data: {
            api_token: apiToken,
            attributes: this.getAttributes()
        }
    }).done((data) => {
        this.populate(data);
        onDone(data);
    });
};

Model.prototype.edit = function (onDone) {
    $.ajax({
        url: `/api/${this.endpoint}`,
        method: "POST",
        data: {
            api_token: apiToken,
            attributes: this.getAttributes(),
        }
    }).done((data) => {
        this.populate(data);
        onDone(data);
    });
};

Model.prototype.destroy = function (onDone) {
    $.ajax({
        url: `/api/${this.endpoint}`,
        method: "DELETE",
        data: {
            api_token: apiToken,
            attributes: this.getAttributes(),
        }
    }).done(function (data) {
        onDone(data);
    });
};

Model.prototype.populate = function (data, that = this) {
    for (var property in data){
        if(!that.hasOwnProperty(property)){
            that[property] = data[property];
        }
    }
};

Model.prototype.attributes = function (onDone) {
    $.ajax({
        url: `/api/${this.endpoint}/attributes`,
        method: "GET",
        data:{
            api_token: apiToken,
        }
    }).done(onDone);
};




// class Model {
//     constructor(endpoint, id = null) {
//         if(id != null){
//             this.id = id;
//         }
//         this.endpoint = endpoint;
//     }
//
//     edit(onDone){
//         var that = this;
//
//         if(this.id==null){
//             var modelAttributes = this.getAttributes();
//             $.ajax({
//                 url: `/api/${this.endpoint}/edit`,
//                 method: "POST",
//                 data: {
//                     api_token: apiToken,
//                     attributes: this.getAttributes(),
//                 }
//             }).done(function (data) {
//                 that.populate(data);
//                 onDone(data);
//             });
//         }else{
//             $.ajax({
//                 url: `/api/${this.endpoint}/${this.id}`,
//                 method: "POST",
//                 data: {
//                     api_token: apiToken,
//                     attributes: this.getAttributes(),
//                 }
//             }).done(function (data) {
//                 that.populate(data);
//                 onDone(data);
//             });
//         }
//     }
//
//     static show(endpoint, attributes, onDone){
//         $.ajax({
//             url: `/api/${endpoint}`,
//             method: "GET",
//             data: {
//                 api_token: apiToken,
//                 attributes: attributes
//             },
//         }).done(function (data) {
//             onDone(data);
//         });
//     }
//
//     get(onDone){
//         var that = this;
//         $.ajax({
//             url: `/api/${this.endpoint}/${this.id}`,
//             method: "GET",
//             data: {
//                 api_token: apiToken,
//             }
//         }).done(function (data) {
//             that.populate(data);
//             onDone(data);
//         });
//     }
//
//     static create(endpoint, attributes = null, onDone){
//         $.ajax({
//             url: `/api/${endpoint}`,
//             method: "POST",
//             data: {
//                 api_token: apiToken,
//                 attributes: attributes,
//             }
//         }).done(function (data) {
//             onDone(data);
//         })
//     }
//
//     destroy(onDone){
//         if(this.id==null){
//             $.ajax({
//                 url: `/api/${this.endpoint}`,
//                 method: "DELETE",
//                 data: {
//                     api_token: apiToken,
//                     attributes: this.getAttributes(),
//                 }
//             }).done(function (data) {
//                 onDone(data);
//             })
//         }else{
//             $.ajax({
//                 url: `/api/${this.endpoint}/${this.id}`,
//                 method: "DELETE",
//                 data: {
//                     api_token: apiToken,
//                 }
//             }).done(function (data) {
//                 onDone(data);
//             })
//         }
//
//     }
//
//     populate(data){
//         for (var property in data){
//             if(property != "id" || data["id"] != 0){
//                 this[property] = data[property];
//             }
//         }
//     }
//
//     getAttributes(){
//         var attributes = {};
//         for(var attribute in this){
//             if(attribute != "endpoint"){
//                 attributes[attribute] = this[attribute];
//             }
//         }
//         return attributes;
//     }
//
// }
var InventoryBay = function (attributes) {
    Model.call(this, attributes);
};

InventoryBay.prototype = Object.create(Model.prototype);
InventoryBay.prototype.constructor = InventoryBay;
InventoryBay.prototype.endpoint = "inventory-bays";
var InventoryBayAssignment = function (attributes) {
    Model.call(this, attributes);
};

InventoryBayAssignment.prototype = Object.create(Model.prototype);
InventoryBayAssignment.prototype.constructor = InventoryBayAssignment;
InventoryBayAssignment.prototype.endpoint = "inventory-bay/assignments";


InventoryBayAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(InventoryBayAssignment.prototype).populate(data, this);
    if(data.bay)this.bay = new InventoryBay(data.bay);
    if(data.product)this.product = new Product(data.product);
};

// class InventoryBayAssignment extends Model{
//     constructor(){
//         super("inventory-bay-assignments");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         super.get(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("inventory-bay-assignments", attributes,function (data) {
//             var assignments = [];
//             data.forEach(function (datum) {
//                 var assignment = new InventoryBayAssignment();
//                 assignment.populate(datum);
//                 assignments.push(assignment);
//             });
//             onDone(assignments);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("inventory-bay-assignments",attributes, function (data) {
//             var assignment = new InventoryBayAssignment();
//             assignment.populate(data);
//             onDone(assignment);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//         var inventoryBay = new InventoryBay(data.bay.id);
//         inventoryBay.populate(data.bay);
//         this.bay = inventoryBay;
//     }
// }
var Product = function (attributes) {
    Model.call(this, attributes);
};

Product.prototype = Object.create(Model.prototype);
Product.prototype.constructor = Product;
Product.prototype.endpoint = "products";

Product.prototype.populate = function (data) {
    Object.getPrototypeOf(Product.prototype).populate(data, this);
    if(data.attribute_assignments)this.attribute_assignments = data.attribute_assignments.map((data) => new ProductAttributeAssignment(data));
    if(data.barcodes)this.barcodes = data.barcodes.map((data) => new ProductBarcode(data));
    if(data.blocks)this.blocks = data.blocks.map((data) => new ProductBlock(data));
    if(data.inventory_bay_assignments)this.inventory_bay_assignments = data.inventory_bay_assignments.map((data) => new InventoryBayAssignment(data));
    if(data.sales_channel_assignments)this.sales_channel_assignments = data.sales_channel_assignments.map((data) => new SalesChannelAssignment(data));
    if(data.child_groups)this.child_groups = data.child_groups.map((data) => new ProductGroup(data));
};

Product.prototype.addAttribute = function(attribute, value, onDone){
    ProductAttributeAssignment.prototype.create({
        product_id: this.id,
        product_attribute_id: attribute,
        value: value
    }, (assignment)=>{
        this.attribute_assignments.push(assignment);
        onDone(assignment);
    })
};

Product.prototype.removeAttribute = function(attribute, onDone){
    this.attribute_assignments.forEach((value, key)=>{
        if(value.attribute.id == attribute){
            value.destroy((data)=>{
                this.attribute_assignments.splice(key, 1);
                onDone(data)
            });
        }
    });

};

Product.prototype.editAttribute = function(attribute, newValue, onDone){
    this.attribute_assignments.forEach((value)=>{
        if(value.attribute.id == attribute){
            value.value = newValue;
            value.edit((data)=>onDone(data));
        }
    });
};

Product.prototype.addBlock = function(reason, salesChannel, onDone){
    ProductBlock.prototype.create({
        product_id: this.id,
        reason_id: reason,
        sales_channel_id: salesChannel,
    }, (block)=>{
        this.blocks.push(block);
        onDone(block);
    })
};

Product.prototype.removeBlock = function(reason, salesChannel, onDone){
    this.blocks.forEach((block, key)=>{
        if(block.reason_id == reason && block.sales_channel_id == salesChannel){
            block.destroy((data)=>{
                this.blocks.splice(key, 1);
                onDone(data)
            })
        }
    });
};

Product.prototype.addInventoryBayAssignment = function(bay, quantity, onDone){
    InventoryBayAssignment.prototype.create({
        inventory_bay_id: bay,
        product_id: this.id,
        quantity: quantity,
    }, (assignment)=>{
        this.inventory_bay_assignments.push(assignment);
        onDone(assignment);
    })
};

Product.prototype.removeInventoryBayAssignment = function(bayID, onDone){
    this.inventory_bay_assignments.forEach((bay, key)=>{
        if(bay.inventory_bay_id == bayID){
            bay.destroy((data)=>{
                this.inventory_bay_assignments.splice(key, 1);
                onDone(data)
            })
        }
    });
};

Product.prototype.editInventoryBayAssignment = function(bayID, quantity, onDone){
    this.inventory_bay_assignments.forEach((bay)=>{
        if(bay.inventory_bay_id == bayID){
            bay.quantity = quantity;
            bay.edit((data)=>{onDone(data)})
        }
    });
};

Product.prototype.addBarcode = function(barcode, quantity, onDone){
    ProductBarcode.prototype.create({
        id: barcode,
        quantity: quantity,
        product_id: this.id,
    }, (barcode)=>{
        this.barcodes.push(barcode);
        onDone(barcode);
    })
};

Product.prototype.removeBarcode = function(barcode, onDone){
    for (let i = 0; i < this.barcodes.length; i++) {
        var barcodeC = this.barcodes[i];
        if(barcodeC.id == barcode){
            barcodeC.destroy((data)=>{
                this.barcodes.splice(i, 1);
                onDone(data);
            });
            return;
        }
    }
};

Product.prototype.editBarcode = function(barcode, quantity, onDone){
    for (let i = 0; i < this.barcodes.length; i++) {
        var barcodeC = this.barcodes[i];
        if(barcodeC.id == barcode){
            this.barcodes[i].quantity = quantity;
            this.barcodes[i].edit((data)=>onDone(data));
            return;
        }
    }

};

Product.prototype.addGroup = function(group, quantity, onDone){
    ProductGroup.prototype.create({
        product_id: group,
        parent_id: this.id,
        quantity: quantity,
    }, (group)=>{
        this.child_groups.push(group);
        onDone(group);
    })
};

Product.prototype.removeGroup = function(groupID, onDone){
    this.child_groups.forEach((group, key)=>{
        if(group.product_id == groupID){
            group.destroy((data)=>{
                this.child_groups.splice(key, 1);
                onDone(data);
            })
        }
    });
};

Product.prototype.editGroup = function(groupID, quantity, onDone){
    this.child_groups.forEach((group)=>{
        if(group.product_id == groupID){
            group.quantity = quantity;
            group.edit((data)=>{onDone(data)})
        }
    });
};



// class Product extends Model{
//     constructor(id){
//         super("products", id);
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         super.get(function (data) {
//             onDone(data);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("products", attributes, function (data) {
//             var product = new Product(data.id);
//             product.populate(data);
//             onDone(product);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//         var newAttributeVals = [];
//         data.attribute_values.forEach((valData) => {
//             newAttributeVals[valData.product_attribute_id] = new ProductAttributeValue();
//             newAttributeVals[valData.product_attribute_id].populate(valData);
//         });
//         this.attribute_values = newAttributeVals;
//         data.barcodes.forEach((barcodeData, key) => {
//             this.barcodes[key] = new ProductBarcode(barcodeData.id);
//             this.barcodes[key].populate(barcodeData);
//         });
//         data.blocks.forEach((blockData, key) => {
//             this.blocks[key] = new ProductBlock();
//             this.blocks[key].populate(blockData);
//         });
//         var newAssignments = [];
//         data.inventory_bay_assignments.forEach((assignmentData, key)=>{
//             newAssignments[assignmentData.bay.id] = new InventoryBayAssignment();
//             newAssignments[assignmentData.bay.id].populate(assignmentData);
//         });
//         this.inventory_bay_assignments = newAssignments;
//         var newSalesChannels = [];
//         data.sales_channels.forEach((scData)=>{
//             newSalesChannels[scData.id] = scData;
//         });
//         this.sales_channels = newSalesChannels;
//         var newChildGroups = [];
//         data.child_groups.forEach((groupData)=>{
//             newChildGroups[groupData.product_id] = new ProductGroup();
//             newChildGroups[groupData.product_id].populate(groupData);
//         });
//         this.child_groups = newChildGroups;
//     }
//
//     addAttribute(attribute, value, onDone){
//         ProductAttributeValue.create({
//             product_id: this.id,
//             product_attribute_id: attribute,
//             value: value
//         }, (attrVal)=>{
//             this.attribute_values[attribute] = attrVal;
//             onDone(attrVal);
//         })
//     }
//
//     removeAttribute(attribute, onDone){
//         this.attribute_values[attribute].destroy((data)=>{
//             this.attribute_values[attribute] = null;
//             onDone(data)
//         });
//     }
//
//     editAttribute(attribute, newValue, onDone){
//         this.attribute_values[attribute].value = newValue;
//         this.attribute_values[attribute].edit((data)=>onDone(data));
//     }
//
//     addBlock(reason, salesChannel, onDone){
//         ProductBlock.create({
//             product_id: this.id,
//             reason_id: reason,
//             sales_channel_id: salesChannel,
//         }, (block)=>{
//             this.blocks.push(block);
//             onDone(block);
//         })
//     }
//
//     removeBlock(reason, salesChannel, onDone){
//         this.blocks.forEach((block, key)=>{
//             if(block.reason_id == reason && block.sales_channel_id == salesChannel){
//                 block.destroy((data)=>{
//                     this.blocks.splice(key, 1);
//                     onDone(data)
//                 })
//             }
//         });
//     }
//
//     addInventoryBayAssignment(bay, quantity, onDone){
//         InventoryBayAssignment.create({
//             inventory_bay_id: bay,
//             product_id: this.id,
//             quantity: quantity,
//         }, (assignment)=>{
//             this.inventory_bay_assignments[assignment.bay.id] = assignment;
//             onDone(assignment);
//         })
//     }
//
//     removeInventoryBayAssignment(bay, onDone){
//         this.inventory_bay_assignments[bay].destroy((data)=>{
//             this.inventory_bay_assignments.splice(bay, 1);
//             onDone(data);
//         })
//     }
//
//     editInventoryBayAssignment(bay, quantity, onDone){
//         this.inventory_bay_assignments[bay].quantity = quantity;
//         this.inventory_bay_assignments[bay].edit((data)=>onDone(data));
//     }
//
//     addBarcode(barcode, quantity, onDone){
//         ProductBarcode.create({
//             id: barcode,
//             quantity: quantity,
//             product_id: this.id,
//         }, (barcode)=>{
//             this.barcodes.push(barcode);
//             onDone(barcode);
//         })
//     }
//
//     removeBarcode(barcode, onDone){
//         for (let i = 0; i < this.barcodes.length; i++) {
//             var barcodeC = this.barcodes[i];
//             if(barcodeC.id == barcode){
//                 barcodeC.destroy((data)=>{
//                     this.barcodes.splice(i, 1);
//                     onDone(data);
//                 });
//                 return;
//             }
//         }
//     }
//
//     editBarcode(barcode, quantity, onDone){
//         for (let i = 0; i < this.barcodes.length; i++) {
//             var barcodeC = this.barcodes[i];
//             if(barcodeC.id == barcode){
//                 this.barcodes[i].quantity = quantity;
//                 this.barcodes[i].edit((data)=>onDone(data));
//                 return;
//             }
//         }
//
//     }
//
//     addGroup(group, quantity, onDone){
//         ProductGroup.create({
//             product_id: group,
//             parent_id: this.id,
//             quantity: quantity,
//         }, (group)=>{
//             this.child_groups[group.product.id] = group;
//             onDone(group);
//         })
//     }
//
//     removeGroup(group, onDone){
//         this.child_groups[group].destroy((data)=>{
//             this.child_groups.splice(group, 1);
//             onDone(data);
//         })
//     }
//
//     editGroup(group, quantity, onDone){
//         this.child_groups[group].quantity = quantity;
//         this.child_groups[group].edit((data)=>onDone(data));
//     }
// }
var ProductAttribute = function (attributes) {
    Model.call(this, attributes);
};

ProductAttribute.prototype = Object.create(Model.prototype);
ProductAttribute.prototype.constructor = ProductAttribute;
ProductAttribute.prototype.endpoint = "product-attributes";


// class ProductAttribute extends Model{
//     constructor(id){
//         super("product-attributes", id);
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         super.get(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-attributes", attributes,function (data) {
//             var productAttributes = [];
//             data.forEach(function (datum) {
//                 var productAttribute = new ProductAttribute(datum.id);
//                 productAttribute.populate(datum);
//                 productAttributes.push(productAttribute);
//             });
//             onDone(productAttributes);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-attributes",attributes, function (data) {
//             var productAttribute = new ProductAttribute(data.id);
//             productAttribute.populate(data);
//             onDone(productAttribute);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//     }
// }
var ProductAttributeAssignment = function (attributes) {
    Model.call(this, attributes);
};

ProductAttributeAssignment.prototype = Object.create(Model.prototype);
ProductAttributeAssignment.prototype.constructor = ProductAttributeAssignment;
ProductAttributeAssignment.prototype.endpoint = "product-attribute-assignments";


ProductAttributeAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(ProductAttributeAssignment.prototype).populate(data, this);
    this.attribute = new ProductAttribute(data.attribute);
};

// class ProductAttributeValue extends Model{
//     constructor(){
//         super("product-attribute-values");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         super.get(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-attribute-values", attributes,function (data) {
//             var productAttributeValues = [];
//             data.forEach(function (datum) {
//                 var productAttributeValue = new ProductAttribute();
//                 productAttributeValue.populate(datum);
//                 productAttributeValues.push(productAttributeValue);
//             });
//             onDone(productAttributeValues);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-attribute-values",attributes, function (data) {
//             var productAttributeValue = new ProductAttributeValue();
//             productAttributeValue.populate(data);
//             onDone(productAttributeValue);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//         var attribute = new ProductAttribute(data.attribute.id);
//         attribute.populate(data.attribute);
//         this.attribute = attribute;
//     }
// }
var ProductBarcode = function (attributes) {
    Model.call(this, attributes);
};

ProductBarcode.prototype = Object.create(Model.prototype);
ProductBarcode.prototype.constructor = ProductBarcode;
ProductBarcode.prototype.endpoint = "product-barcodes";



// class ProductBarcode extends Model{
// //     constructor(id){
// //         super("product-barcodes", id);
// //     }
// //
// //     edit(onDone){
// //         super.edit(function (data) {
// //             onDone(data);
// //         })
// //     }
// //
// //     get(onDone){
// //         super.get(function (data) {
// //             onDone(data);
// //         })
// //     }
// //
// //     static show(attributes, onDone){
// //         super.show("product-barcodes", attributes, function (data) {
// //             var productBarcodes = [];
// //             data.forEach(function (datum) {
// //                 var productBarcode = new ProductBarcode(datum.id);
// //                 productBarcode.populate(datum);
// //                 productBarcodes.push(productBarcode);
// //             });
// //             onDone(productBarcodes);
// //         })
// //     }
// //
// //     static create(attributes, onDone){
// //         super.create("product-barcodes",attributes, function (data) {
// //             var barcode = new ProductBarcode(data.id);
// //             barcode.populate(data);
// //             onDone(barcode);
// //         })
// //     }
// //
// //     destroy(onDone){
// //         super.destroy(function (data) {
// //             onDone(data);
// //         })
// //     }
// // }
var ProductBlock = function (attributes) {
    Model.call(this, attributes);
};

ProductBlock.prototype = Object.create(Model.prototype);
ProductBlock.prototype.constructor = ProductBlock;
ProductBlock.prototype.endpoint = "product-blocks";


// class ProductBlock extends Model{
//     constructor(){
//         super("product-blocks");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-blocks", attributes, function (data) {
//             var productBlocks = [];
//             data.forEach(function (datum) {
//                 var productBlock = new ProductBlock();
//                 productBlock.populate(datum);
//                 productBlocks.push(productBlock);
//             });
//             onDone(productBlocks);
//         });
//     }
//
//     static reasons(onDone){
//         $.ajax({
//             url: `/api/product-blocks/reasons`,
//             method: "GET",
//             data: {
//                 api_token: apiToken,
//             },
//         }).done(function (data) {
//             onDone(data);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-blocks", attributes,function (data) {
//             var productBlock = new ProductBlock();
//             productBlock.populate(data);
//             onDone(productBlock);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//}
var ProductBlockReason = function (attributes) {
    Model.call(this, attributes);
};

ProductBlockReason.prototype = Object.create(Model.prototype);
ProductBlockReason.prototype.constructor = ProductBlockReason;
ProductBlockReason.prototype.endpoint = "product-block-reasons";

var ProductGroup = function (attributes) {
    Model.call(this, attributes);
};

ProductGroup.prototype = Object.create(Model.prototype);
ProductGroup.prototype.constructor = ProductGroup;
ProductGroup.prototype.endpoint = "product-groups";


// class ProductGroup extends Model{
//     constructor(){
//         super("product-groups");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-groups", attributes, function (data) {
//             var productGroups = [];
//             data.forEach(function (datum) {
//                 var productGroup = new ProductGroup();
//                 productGroup.populate(datum);
//                 productGroups.push(productGroup);
//             });
//             onDone(productGroups);
//         });
//     }
//
//     static create(attributes, onDone){
//         super.create("product-groups", attributes,function (data) {
//             var productGroup = new ProductGroup();
//             productGroup.populate(data);
//             onDone(productGroup);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
// }
var ProductRange = function (attributes) {
    Model.call(this, attributes);
};

ProductRange.prototype = Object.create(Model.prototype);
ProductRange.prototype.constructor = ProductRange;
ProductRange.prototype.endpoint = "product-ranges";


ProductRange.prototype.populate = function (data) {
    Object.getPrototypeOf(ProductRange.prototype).populate(data, this);
    data.products.forEach((productData, key) => {
        this.products[key] = new Product(productData.id);
        this.products[key].populate(productData);
    });
};

// class ProductRange extends Model{
//     constructor(id){
//         super( "product-ranges", id);
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         var that = this;
//         super.get(function (data) {
//
//             onDone(data);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-ranges",attributes,  function (data) {
//             var productRange = new ProductRange(data.id);
//             productRange.populate(data);
//             onDone(productRange);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//         this.products.forEach((productData, key) => {
//             this.products[key] = new Product(productData.id);
//             this.products[key].populate(productData);
//         });
//     }
// }
var SalesChannelAssignment = function (attributes) {
    Model.call(this, attributes);
};

SalesChannelAssignment.prototype = Object.create(Model.prototype);
SalesChannelAssignment.prototype.constructor = SalesChannelAssignment;
SalesChannelAssignment.prototype.endpoint = "sales-channel-assignments";

SalesChannelAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(SalesChannelAssignment.prototype).populate(data, this);
    // this.sales_channel = new SalesChannel(data.bay);
};