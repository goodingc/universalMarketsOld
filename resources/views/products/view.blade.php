<script>
    var product;
    apiToken = "{{\Illuminate\Support\Facades\Auth::user()->api_token}}";
    $(function () {

        $("a[href='#nav-attributes']").on("shown.bs.tab", () => tables.attributes.columns.adjust());
        $("a[href='#nav-blocks']").on("shown.bs.tab", () => tables.blocks.columns.adjust());
        $("a[href='#nav-suppliers']").on("shown.bs.tab", () => tables.suppliers.columns.adjust());
        $("a[href='#nav-salesChannels']").on("shown.bs.tab", () => tables.salesChannels.columns.adjust());
        $("a[href='#nav-inventoryBays']").on("shown.bs.tab", () => tables.inventoryBays.columns.adjust());
        $("a[href='#nav-barcodes']").on("shown.bs.tab", () => tables.barcodes.columns.adjust());
        $("a[href='#nav-groups']").on("shown.bs.tab", () => tables.groups.columns.adjust());

        var tableHeight = $(".card-hero > .card-body").height()-$(".breadcrumb").height()-$(".nav-tabs").height()-145;

        var tables = {
            attributes: $("#attributesTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "attribute.title"},
                    {data: "value"},
                    {data: (row) => {
                        return `<i id="attribute-${row.attribute.id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>
                                <a tabindex="0" id="attribute-${row.attribute.id}-pop" data-toggle="popover" data-placement="top" data-html="true" data-content="
                                    <div class='input-group'>
                                        <input id='attribute-${row.attribute.id}-value' type='text' placeholder='New value' value='${row.value}' class='form-control'>
                                        <div class='input-group-append'>
                                            <button id='attribute-${row.attribute.id}-edit' class='btn btn-primary'>Save</button>
                                        </div>
                                    </div>
                                " class="ml-auto text-danger"><i class="fas fa-edit fa-lg text-secondary"></i></a>`
                    },
                    width: 40,
                    },
                ],
            }),
            blocks: $("#blocksTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "reason.reason"},
                    {data: (row)=>{
                        if(row.sales_channel_id == 0) return "All";
                        return product.sales_channel_assignments.filter((assignment)=> row.sales_channel_id == assignment.sales_channel.id)[0].sales_channel.title;
                    }},
                    {data: (row) => {
                            return `<i id="block-${row.reason_id}-${row.sales_channel_id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>`
                        },
                        width: 40,
                    },
                ],
            }),
            suppliers: $("#suppliersTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "id"},
                    {data: "name"},
                    {data: "pivot.cost_price"},
                    {data: "pivot.supplier_stock_level"},
                ],
            }),
            salesChannels: $("#salesChannelsTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "sales_channel.id"},
                    {data: "sales_channel.title"},
                    {data: "sell_price_ex_vat"},
                    {data: "default_margin_percent"},
                ],
            }),
            inventoryBays: $("#inventoryBaysTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "bay.warehouse.name"},
                    {data: "bay.name"},
                    {data: "quantity"},
                    {data: (row) => {
                            return `<i id="bay-${row.bay.id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>
                                    <a tabindex="0" id="bay-${row.bay.id}-pop" data-toggle="popover" data-placement="top" data-html="true" data-content="
                                        <div class='input-group'>
                                            <input id='bay-${row.bay.id}-quantity' type='number' placeholder='New quantity' value='${row.quantity}' class='form-control'>
                                            <div class='input-group-append'>
                                                <button id='bay-${row.bay.id}-edit' class='btn btn-primary'>Save</button>
                                            </div>
                                        </div>
                                    " class="ml-auto text-danger"><i class="fas fa-edit fa-lg text-secondary"></i></a>`
                        },
                        width: 40,
                    },
                ],
            }),
            barcodes: $("#barcodesTable").DataTable({
                paging: false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "quantity"},
                    {data: "id"},
                    {
                        data: (row) => {
                            return `<i id="barcode-${row.id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>
                                    <a tabindex="0" id="barcode-${row.id}-pop" data-toggle="popover" data-placement="top" data-html="true" data-content="
                                        <div class='input-group'>
                                            <input id='barcode-${row.id}-quantity' type='number' placeholder='New quantity' value='${row.quantity}' class='form-control'>
                                            <div class='input-group-append'>
                                                <button id='barcode-${row.id}-edit' class='btn btn-primary'>Save</button>
                                            </div>
                                        </div>
                                    " class="ml-auto text-danger"><i class="fas fa-edit fa-lg text-secondary"></i></a>`
                        },
                        width: 40,
                    },
                ],
            }),
                groups: $("#groupsTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "product.title"},
                    {data: "quantity"},
                    {data: (row) => {
                            return `<i id="group-${row.product.id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>
                                    <a tabindex="0" id="group-${row.product.id}-pop" data-toggle="popover" data-placement="top" data-html="true" data-content="
                                        <div class='input-group'>
                                            <input id='group-${row.product.id}-quantity' type='number' placeholder='New quantity' value='${row.quantity}' class='form-control'>
                                            <div class='input-group-append'>
                                                <button id='group-${row.product.id}-edit' class='btn btn-primary'>Save</button>
                                            </div>
                                        </div>
                                    " class="ml-auto text-danger"><i class="fas fa-edit fa-lg text-secondary"></i></a>`
                        },
                        width: 40,
                    },
                ],
            }),
        };

        tables.attributes.on("draw", function () {
            $("[id^=attribute][id$=delete]").on("click", function () {
                product.removeAttribute($(this).attr("id").split("-")[1], () => populate(product))
            });
            $("[id^=attribute][id$=pop]").popover({
                trigger: "click",
                boundary: "body"
            }).on("shown.bs.popover", function () {
                var that = $(this);
                var id = $(this).attr("id").split("-")[1];
                $(`#attribute-${id}-edit`).on("click", function () {
                    product.editAttribute(id, $(`#attribute-${id}-value`).val(), (data) => {
                        that.popover("hide");
                        populate(product)
                    });
                })
            })
        });

        tables.blocks.on("draw", function () {
            $("[id^=block][id$=delete]").on("click", function () {
                var split = $(this).attr("id").split("-");
                product.removeBlock(split[1], split[2], () => populate(product))
            });
        });

        tables.inventoryBays.on("draw", function () {
            $("[id^=bay][id$=delete]").on("click", function () {
                var split = $(this).attr("id").split("-");
                product.removeInventoryBayAssignment(split[1], () => populate(product))
            });
            $("[id^=bay][id$=pop]").popover({
                trigger: "click",
                boundary: "body"
            }).on("shown.bs.popover", function () {
                var that = $(this);
                var id = $(this).attr("id").split("-")[1];
                $(`#bay-${id}-edit`).on("click", function () {
                    product.editInventoryBayAssignment(id, $(`#bay-${id}-quantity`).val(), (data) => {
                        that.popover("hide");
                        populate(product)
                    });
                })
            })
        });

        tables.barcodes.on("draw", function () {
            $("[id^=barcode][id$=delete]").on("click", function () {
                var split = $(this).attr("id").split("-");
                product.removeBarcode(split[1], () => populate(product))
            });
            $("[id^=barcode][id$=pop]").popover({
                trigger: "click",
                boundary: "body"
            }).on("shown.bs.popover", function () {
                var that = $(this);
                var id = $(this).attr("id").split("-")[1];
                $(`#barcode-${id}-edit`).on("click", function () {
                    product.editBarcode(id, $(`#barcode-${id}-quantity`).val(), (data) => {
                        that.popover("hide");
                        populate(product)
                    });
                })
            })
        });

        tables.groups.on("draw", function () {
            $("[id^=group][id$=delete]").on("click", function () {
                var split = $(this).attr("id").split("-");
                product.removeGroup(split[1], () => populate(product))
            });
            $("[id^=group][id$=pop]").popover({
                trigger: "click",
                boundary: "body"
            }).on("shown.bs.popover", function () {
                var that = $(this);
                var id = $(this).attr("id").split("-")[1];
                $(`#group-${id}-edit`).on("click", function () {
                    product.editGroup(id, $(`#group-${id}-quantity`).val(), (data) => {
                        that.popover("hide");
                        populate(product)
                    });
                })
            })
        });

        product = new Product({{$product->id}});
        product.get(() => populate(product));

        function populate(data) {
            $("#attributesTable_wrapper > .row > .col-sm-12.col-md-7").html("<div class='row justify-content-center mt-3 text-secondary'>Loading Options...</div>");
            $("#blocksTable_wrapper > .row > .col-sm-12.col-md-7").html("<div class='row justify-content-center mt-3 text-secondary'>Loading Options...</div>");
            $("#inventoryBaysTable_wrapper > .row > .col-sm-12.col-md-7").html("<div class='row justify-content-center mt-3 text-secondary'>Loading Options...</div>");
            $("#idField").val(data.id);
            $("#skuField").val(data.sku);
            $("#skuBreadcrumb").html(data.sku);
            $("#titleField").val(data.title);
            $("#stockOnHandField").val(data.stockOnHand);
            $("#supplierStockField").val(data.supplierStock);
            $("#taxRateNameField").val(data.tax_rate.id || 0);
            $("#taxRateValueField").html((data.tax_rate.tax_rate || 0) + "%");
            $("#heightField").val(data.shipping_height);
            $("#lengthField").val(data.shipping_length);
            $("#widthField").val(data.shipping_width);
            $("#shippingWeightField").val(data.shipping_weight_grams);
            $("#largeLetterField").prop("checked", data.large_letter_compatible);
            $("#packagingTypeField").val(data.packaging_type);
            console.log(data);
            for(var table in tables) {tables[table].clear();}
            tables.attributes.rows.add(data.attribute_assignments);
            tables.blocks.rows.add(data.blocks);
            tables.suppliers.rows.add(data.suppliers);
            tables.salesChannels.rows.add(data.sales_channel_assignments);
            tables.inventoryBays.rows.add(data.inventory_bay_assignments);
            tables.barcodes.rows.add(data.barcodes);
            tables.groups.rows.add(data.child_groups);
            for(var table in tables) {tables[table].draw()}

            ProductAttribute.prototype.show(null, (attrs)=>{
                var options;
                attrs.forEach((attr)=>{
                    options += `<option value="${attr.id}">${attr.title}</option>`;
                });
                $("#attributesTable_wrapper > .row > .col-sm-12.col-md-7").html(`
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <select id="addAttributeSelect" class="custom-select form-control">
                            ${options}
                        </select>
                    </div>
                    <input id="addAttributeValue" type="text" class="form-control" aria-label="Text input with dropdown button">
                    <div class="input-group-append">
                        <button id="addAttributeTrigger" class="btn btn-primary">Add</button>
                    </div>
                </div>
                `);
                $("#addAttributeTrigger").on("click", function () {
                    product.addAttribute( $("#addAttributeSelect").val(), $("#addAttributeValue").val(), (data) => populate(product));
                })
            });

            ProductBlockReason.prototype.show(null, (reasons)=>{
                var blockOptions;
                reasons.forEach((reason)=>{
                    blockOptions += `<option value="${reason.id}">${reason.reason}</option>`;
                });
                var channelOptions = "<option value=0>All</option>";
                product.sales_channel_assignments.forEach((assignment)=>{
                    channelOptions += `<option value="${assignment.sales_channel.id}">${assignment.sales_channel.title}</option>`;
                });
                $("#blocksTable_wrapper > .row > .col-sm-12.col-md-7").html(`
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <select id="addBlockChannel" class="custom-select form-control">
                            ${channelOptions}
                        </select>
                    </div>
                    <select id="addBlockReason" class="custom-select form-control">
                            ${blockOptions}
                    </select>
                    <div class="input-group-append">
                        <button id="addBlockTrigger" class="btn btn-primary">Add</button>
                    </div>
                </div>`
                );
                $("#addBlockTrigger").on("click", function () {
                    product.addBlock($("#addBlockReason").val(), $("#addBlockChannel").val(), (block)=>populate(product));
                });
            });

            InventoryBay.prototype.show(null, (bays)=>{
                let bayOptions;
                bayOptions = bays.reduce((prevBays, bay)=>prevBays+`<option value="${bay.id}">${bay.name}@${bay.warehouse?bay.warehouse.name:"No warehouse"}</option>`);
                $("#inventoryBaysTable_wrapper > .row > .col-sm-12.col-md-7").html(`
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <select id="addBay" class="custom-select form-control">
                            ${bayOptions}
                        </select>
                    </div>
                    <input id="addBayQuantity" type="number" placeholder="Bay Quantity" class="form-control">
                    <div class="input-group-append">
                        <button id="addBayTrigger" class="btn btn-primary">Add</button>
                    </div>
                </div>`
                );
                $("#addBayTrigger").on("click", function () {
                    product.addInventoryBayAssignment($("#addBay").val(), $("#addBayQuantity").val(), (bay)=>populate(product));
                });
            })
        }




        $("#barcodesTable_wrapper > .row > .col-sm-12.col-md-7").html(`
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <input id="addBarcodeID" placeholder="Barcode" type="number" class="form-control">
                    </div>
                    <input id="addBarcodeQuantity" placeholder="Product Quantity" type="number" class="form-control">
                    <div class="input-group-append">
                        <button id="addBarcodeTrigger" class="btn btn-primary">Add</button>
                    </div>
                </div>`
        );
        $("#addBarcodeTrigger").on("click", function () {
            product.addBarcode($("#addBarcodeID").val(), $("#addBarcodeQuantity").val(), (barcode)=>populate(product));
        });

        $("#groupsTable_wrapper > .row > .col-sm-12.col-md-7").html(`
                <div class="input-group mt-2">
                    <div class="input-group-prepend">
                        <input id="addGroupSKU" placeholder="Product SKU" type="text" class="form-control">
                    </div>
                    <input id="addGroupQuantity" placeholder="Product Quantity" type="number" class="form-control">
                    <div class="input-group-append">
                        <button id="addGroupTrigger" disabled class="btn btn-primary">Add</button>
                    </div>
                </div>`
        );
        $("#addGroupSKU").on("change", function () {
            if($(this).val() !== ""){
                Product.prototype.show({sku: $(this).val()}, (data)=>{
                    if(data.length === 1){
                        $(this).addClass("bg-success");
                        $("#addGroupTrigger").attr("disabled", false).on("click", function () {
                            product.addGroup(data[0].id, $("#addGroupQuantity").val(), (group)=>populate(product));
                        });
                    }else {
                        $(this).addClass("bg-danger");
                        $("#addGroupTrigger").attr("disabled", true).off("click");
                    }
                })
            }else{
                $(this).removeClass("bg-success");
                $(this).removeClass("bg-danger");
                $("#addGroupTrigger").attr("disabled", true).off("click");
            }
        });


        var editors = {
            sku: new Editor({
                input: $("#skuField"),
                trigger: $("#skuTrigger"),
            }).triggered(function (val) {
                product.sku = val;
                product.edit(() => populate(product));
            }),
            title: new Editor({
                input: $("#titleField"),
                trigger: $("#titleTrigger"),
            }).triggered(function (val) {
                product.title = val;
                product.edit(() => populate(product));
            }),
            taxRate: new Editor({
                input: $("#taxRateNameField"),
                trigger: $("#taxRateTrigger"),
            }).triggered(function (val) {
                product.tax_rate_id = val;
                product.edit(() => populate(product));
            }),
            largeLetter: new Editor({
                input: $("#largeLetterField"),
                trigger: $("#largeLetterTrigger"),
            }).triggered(function (val) {
                product.large_letter_compatible = $("#largeLetterField").is(":checked")?1:0;
                product.edit(() => populate(product));
            }),
            packagingType: new Editor({
                input: $("#packagingTypeField"),
                trigger: $("#packagingTypeTrigger"),
            }).triggered(function (val) {
                product.packaging_type = val;
                product.edit(() => populate(product));
            }),
            shippingWeight: new Editor({
                input: $("#shippingWeightField"),
                trigger: $("#shippingWeightTrigger"),
            }).triggered(function (val) {
                product.shipping_weight_grams = val;
                product.edit(() => populate(product));
            })
        };

        $("#addButton").on("click", function () {
            Product.create(function (product) {
                getUI(product.id, true);
            })
        });

        $("#deletePopover").popover({
            trigger: "click",
            boundary: "body"
        }).on("shown.bs.popover", function () {
            $("#deleteTrigger").on("click", function () {
                if ($("#deleteConfirm").val() == product.sku) {
                    product.destroy(function () {
                        window.location = "/products";
                    });
                }
            });
        });
    });

</script>

<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{isset($product->range)?route("productRanges.viewPreload", ["id" => $product->range->id]):"#"}}">{{isset($product->range)?$product->range->sku:"No Range"}}</a>
        </li>
        <li id="skuBreadcrumb" class="breadcrumb-item active"></li>
        <li id="deletePopover" data-toggle="popover" data-placement="top" data-html="true" data-content="
            <div class='input-group'>
                <input id='deleteConfirm' type='text' placeholder='Type SKU to confirm' class='form-control'>
                <div class='input-group-append'>
                    <button id='deleteTrigger' class='btn btn-danger'>Delete</button>
                </div>
            </div>
        " class="ml-auto text-danger"><i class="fas fa-minus-circle fa-lg"></i></li>
        <li id="addButton" class="ml-2 text-success"><i class="fas fa-plus-circle fa-lg"></i></li>
    </ol>
</nav>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-12">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" data-toggle="tab" href="#nav-details" role="tab">Details</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-attributes" role="tab">Attributes</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-blocks" role="tab">Product Blocks</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-suppliers" role="tab">Suppliers</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-salesChannels" role="tab">Sales Channels</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-inventoryBays" role="tab">Inventory Bays</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-barcodes" role="tab">Barcodes</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-groups" role="tab">Groups</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-details" role="tabpanel">
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">ID</span>
                                            </div>
                                            <input id="idField" disabled type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">SKU</span>
                                            </div>
                                            <input id="skuField" disabled type="text" class="form-control">
                                            <div class="input-group-append">
                                                <button id="skuTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Title</span>
                                            </div>
                                            <input id="titleField" disabled type="text" class="form-control">
                                            <div class="input-group-append">
                                                <button id="titleTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Stock on Hand</span>
                                            </div>
                                            <input id="stockOnHandField" disabled type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Supplier Stock</span>
                                            </div>
                                            <input id="supplierStockField" disabled type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span
                                                        class="input-group-text">Tax Rate</span></div>
                                            <select id="taxRateNameField" class="form-control" disabled>
                                                @foreach(\App\Models\TaxRate::all("id", "name") as $taxRate)
                                                    <option value="{{$taxRate->id}}">{{$taxRate->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <span id="taxRateValueField" class="input-group-text"></span>
                                                <button id="taxRateTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Shipping Dimensions</span>
                                            </div>
                                            <input id="heightField" disabled type="text" class="form-control">
                                            <span class="input-group-text rounded-0">x</span>
                                            <input id="lengthField" disabled type="text" class="form-control">
                                            <span class="input-group-text rounded-0">x</span>
                                            <input id="widthField" disabled type="text" class="form-control">
                                            <div class="input-group-append">
                                                <button class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Shipping Weight</span>
                                            </div>
                                            <input id="shippingWeightField" disabled type="text" class="form-control">
                                            <div class="input-group-append">
                                                <button id="shippingWeightTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Large Letter Compatible</span>
                                            </div>
                                            <input id="largeLetterField" disabled type="checkbox" class="form-control">
                                            <div class="input-group-append">
                                                <button id="largeLetterTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend"><span class="input-group-text">Packaging Type</span>
                                            </div>
                                            <input id="packagingTypeField" disabled type="text" class="form-control">
                                            <div class="input-group-append">
                                                <button id="packagingTypeTrigger" class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">Product Range</span>
                                            </div>
                                            <input disabled type="text" value="{{isset($product->range)?$product->range->sku:"No Range"}}"
                                                   class="form-control">
                                            <div class="input-group-append">
                                                <button class="btn">Edit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-attributes" role="tabpanel">
                        <table id="attributesTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                                <th scope="col">Action</th>

                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-blocks" role="tabpanel">
                        <table id="blocksTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Reason</th>
                                <th scope="col">Sales Channel</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-suppliers" role="tabpanel">
                        <table id="suppliersTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Cost Price</th>
                                <th scope="col">Stock Level</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-salesChannels" role="tabpanel">
                        <table id="salesChannelsTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Title</th>
                                <th scope="col">Sell Price</th>
                                <th scope="col">Default Margin</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-inventoryBays" role="tabpanel">
                        <table id="inventoryBaysTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Warehouse</th>
                                <th scope="col">Bay</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-barcodes" role="tabpanel">
                        <table id="barcodesTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Quantity</th>
                                <th scope="col">Barcode</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-groups" role="tabpanel">
                        <table id="groupsTable" class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Child</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>