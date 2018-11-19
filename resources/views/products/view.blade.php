<script>
    var product;
    apiToken = "{{\Illuminate\Support\Facades\Auth::user()->api_token}}";
    $(function () {

        $("a[href='#nav-attributes']").on("shown.bs.tab", () => tables.attributes.columns.adjust());
        $("a[href='#nav-suppliers']").on("shown.bs.tab", () => tables.suppliers.columns.adjust());
        $("a[href='#nav-salesChannels']").on("shown.bs.tab", () => tables.salesChannels.columns.adjust());
        $("a[href='#nav-inventoryBays']").on("shown.bs.tab", () => tables.inventoryBays.columns.adjust());
        $("a[href='#nav-barcodes']").on("shown.bs.tab", () => tables.barcodes.columns.adjust());

        var tableHeight = $(".card-hero > .card-body").height()-$(".breadcrumb").height()-$(".nav-tabs").height()-145;

        var tables = {
            attributes: $("#attributesTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "title"},
                    {data: "pivot.value"},
                    {data: (row) => {
                        return `<i id="attribute-${row.id}-delete" class="fas fa-minus-circle fa-lg text-danger"></i>`
                    }},
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
                    {data: "id"},
                    {data: "title"},
                    {data: "pivot.sell_price_ex_vat"},
                    {data: "pivot.default_margin_percent"},
                ],
            }),
            inventoryBays: $("#inventoryBaysTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "warehouse_id"},
                    {data: "name"},
                    {data: "pivot.quantity"},
                ],
            }),
            barcodes: $("#barcodesTable").DataTable({
                paging:false,
                searching: false,
                responsive: true,
                scrollY: tableHeight,
                columns: [
                    {data: "quantity"},
                    {data: "barcode"},
                ],
            }),
        };

        tables.attributes.on("draw", function () {
            $("[id^=attribute][id$=delete]").on("click", function () {
                product.removeAttribute($(this).attr("id").split("-")[1], (data) => populate(data))
            });
        });




        ProductAttribute.show((attrs)=>{
            var options;
            attrs.forEach((attr)=>{
                options += `<option value="${attr.id}">${attr.data.title}</option>`;
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
                product.addAttribute({
                    product_attribute_id: $("#addAttributeSelect").val(),
                    value: $("#addAttributeValue").val()
                }, (data) => populate(data))
            })
        });



        product = new Product({{$product->id}});
        product.get((data) => populate(data));

        function populate(data) {
            $("#idField").val(data.id);
            $("#skuField").val(data.sku);
            $("#skuBreadcrumb").html(data.sku);
            $("#titleField").val(data.title);
            $("#stockOnHandField").val(data.stockOnHand);
            $("#supplierStockField").val(data.supplierStock);
            $("#taxRateNameField").val(data.tax_rate.id);
            $("#taxRateValueField").html(data.tax_rate.tax_rate + "%");
            $("#heightField").val(data.shipping_height);
            $("#lengthField").val(data.shipping_length);
            $("#widthField").val(data.shipping_width);
            $("#largeLetterField").prop("checked", data.large_letter_compatible);
            $("#packagingTypeField").val(data.packaging_type);
            console.log(data);
            for(var table in tables) {tables[table].clear();}
            tables.attributes.rows.add(data.product_attributes);
            tables.suppliers.rows.add(data.suppliers);
            tables.salesChannels.rows.add(data.sales_channels);
            tables.inventoryBays.rows.add(data.inventory_bays);
            tables.barcodes.rows.add(data.barcodes);
            for(var table in tables) {tables[table].draw()}
        }

        var editors = {
            sku: new Editor({
                input: $("#skuField"),
                trigger: $("#skuTrigger"),
            }).triggered(function (val) {
                product.data.sku = val;
                product.edit((data) => populate(data));
            }),
            title: new Editor({
                input: $("#titleField"),
                trigger: $("#titleTrigger"),
            }).triggered(function (val) {
                product.data.title = val;
                product.edit((data) => populate(data));
            }),
            taxRate: new Editor({
                input: $("#taxRateNameField"),
                trigger: $("#taxRateTrigger"),
            }).triggered(function (val) {
                product.data.tax_rate_id = val;
                product.edit((data) => populate(data));
            }),
            largeLetter: new Editor({
                input: $("#largeLetterField"),
                trigger: $("#largeLetterTrigger"),
            }).triggered(function (val) {
                product.data.large_letter_compatible = $("#largeLetterField").is(":checked")?1:0;
                product.edit((data) => populate(data));
            }),
            packagingType: new Editor({
                input: $("#packagingTypeField"),
                trigger: $("#packagingTypeTrigger"),
            }).triggered(function (val) {
                product.data.packaging_type = val;
                product.edit((data) => populate(data));
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
                if ($("#deleteConfirm").val() == product.data.sku) {
                    product.destroy(function () {
                        window.location = "/products";
                    })
                }
            })
        })
    })

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
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-suppliers" role="tab">Suppliers</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-salesChannels" role="tab">Sales Channels</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-inventoryBays" role="tab">Inventory Bays</a>
                    <a class="nav-item nav-link" data-toggle="tab" href="#nav-barcodes" role="tab">Barcodes</a>
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
                                <th scope="col">Name</th>
                                <th scope="col">Quantity</th>
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