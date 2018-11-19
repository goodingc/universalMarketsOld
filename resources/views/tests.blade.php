@extends('layouts.app')

@section("head")
    <script defer>
        var stockLevelFileOptions = [];
        var files = [];
        var formData = new FormData();
        var sse;
        $(function () {
            sse = $.SSE("{{route("suppliers.updateStockLevels", ["api_token"=>Auth::user()->api_token])}}", {
                onMessage: function (message) {
                    var output = $("#output");
                    output.append(JSON.parse(message.data) + "<br>");
                    output[0].scrollTop = output[0].scrollHeight;
                },
                events: {
                    endStream: function (data) {
                        sse.stop();
                    },
                },

            });

            $(document).on('change', ':file', function () {
                var input = $(this);
                input.trigger('fileselect', [input]);
            });

            $(':file').on('fileselect', function (event, fileSelect) {
                var numFiles = fileSelect.get(0).files ? fileSelect.get(0).files.length : 1,
                    label = fileSelect.val().replace(/\\/g, '/').replace(/.*\//, '');
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log) alert(log);
                }

                var fileIndex = 0;
                var reader = new FileReader();
                reader.onload = function (event) {
                    var contents = event.target.result;
                    files.push({
                        name: fileSelect.get(0).files[fileIndex].name,
                        headers: contents.substr(0, contents.indexOf('\n')).split(","),
                    });
                    if (fileSelect.get(0).files[++fileIndex]) {
                        reader.readAsText(fileSelect.get(0).files[fileIndex]);
                    } else {
                        setStockLevelFileHeaderTable(0);
                    }
                };
                reader.readAsText(fileSelect.get(0).files[fileIndex]);
                var stockLevelFileSelect = $("#stockLevelFileSelect");
                Array.from(fileSelect.get(0).files).forEach(function (file) {
                    var option = new Option(file.name);
                    option.setAttribute("style", "color: #761b18; background-color: #f9d6d5;");
                    stockLevelFileOptions.push(option);
                    stockLevelFileSelect.append(option);
                    formData.append("stockLevelFiles[]", file, file.name);
                });
                stockLevelFileSelect.parent().show();
                $("#stockLevelHeaders").parent().show();
                $("#stockLevelFileUpload").parent().show();
            });

            $("#stockLevelFileSelect").on("change", function () {
                setStockLevelFileHeaderTable($(this).prop("selectedIndex"))
            });

            $("#stockLevelFileUpload").on("click", function () {
                formData.append("headerData", JSON.stringify(files));
                $.ajax({
                    type: "POST",
                    url: "{{route("suppliers.uploadStockFiles", ["api_token"=>Auth::user()->api_token])}}",
                    data: formData,
                    processData: false,
                    contentType: false,

                }).done(function () {
                    sse.start();
                });
            });

            $("#getOrders").on("click", function () {
                $.ajax({
                    url: "{{route("orders.get", ["api_token"=>Auth::user()->api_token])}}",
                    method: "GET",
                }).done(function (data) {
                    var output = $("#output");
                    var outputContents = "<div class='list-group'>";
                    JSON.parse(data).forEach(function (order) {
                        outputContents+="<div class='list-group-item'>";
                        outputContents+="Order #: "+order.id+"<br>";
                        outputContents+="Purchase Date: "+order.purchaseDate+"<br>";
                        outputContents+="Last Update Date: "+order.lastUpdateDate+"<br>";
                        outputContents+="Status: "+order.status+"<br>";
                        outputContents+="Total: "+order.total.CurrencyCode+" "+order.total.Amount+"<br>";
                        outputContents+="</div>";
                    });
                    outputContents+="</div>";
                    output.append(outputContents);
                    output[0].scrollTop = output[0].scrollHeight;
                })
            });

            $("#productSearch").on("click", function () {
                $.ajax({
                    url: "{{route("products.search", ["api_token"=>Auth::user()->api_token])}}",
                    method: "GET",
                    data: {
                        query: $("#productSearchQuery").val(),
                    }
                }).done(function (data) {
                    var output = $("#output");

                    var outputContents = "<div class='list-group'>";
                    data.forEach(function (order) {
                        outputContents+="<div class='list-group-item'><pre>";
                        outputContents+= JSON.stringify(order, undefined, 2);
                        outputContents+="</pre></div>";
                    });
                    outputContents+="</div>";
                    output.append(outputContents);
                    output[0].scrollTop = output[0].scrollHeight;
                })
            });

            $("#recommendationsList").on("click", function () {
                $.ajax({
                    url: "{{route("recommendations.list", ["api_token"=>Auth::user()->api_token])}}",
                    method: "GET",
                }).done(function (data) {
                    var output = $("#output");
                    output.append("<pre>"+JSON.stringify(data, undefined, 2)+"</pre>");
                    output[0].scrollTop = output[0].scrollHeight;
                })
            });

            $("#fulfillmentOrdersList").on("click", function () {
                $.ajax({
                    url: "{{route("fulfillmentOrders.list", ["api_token"=>Auth::user()->api_token])}}",
                    method: "GET",
                }).done(function (data) {
                    var output = $("#output");
                    output.append("<pre>"+JSON.stringify(data, undefined, 2)+"</pre>");
                    output[0].scrollTop = output[0].scrollHeight;
                })
            });

            $("#inventoryReport").on("click", function () {
                $.ajax({
                    url: "{{route("feed.inventory", ["api_token"=>Auth::user()->api_token])}}",
                    data: {
                        vendor: $("#inventoryReportVendor").val(),
                        count: $("#inventoryReportItemCount").val()
                    },
                    method: "GET",
                }).done(function (data) {
                    var output = $("#output");
                    output.append(data);
                    output[0].scrollTop = output[0].scrollHeight;
                })
            });

        });

        function setStockLevelFileHeaderTable(fileID) {
            table = $("#stockLevelHeaders tbody");
            table.children().remove();
            files[fileID].headers.forEach(function (header, index) {
                table.append("<tr>" +
                    "<td>" + index + "</td>" +
                    "<td>" + header + "</td>" +
                    "<td><input id='" + fileID + "-" + index + "-sku' type='radio' name='sku' class='form-check-input mx-auto' " + (index == files[fileID].sku ? "checked" : (index == files[fileID].quantity ? "disabled" : "")) + "></td>" +
                    "<td><input id='" + fileID + "-" + index + "-qty' type='radio' name='quantity' class='form-check-input mx-auto' " + (index == files[fileID].sku ? "disabled" : (index == files[fileID].quantity ? "checked" : "")) + "></td>" +
                    "</tr>")
            });
            $("[id$='qty'], [id$='sku']").on("click", function () {
                var ref = $(this).attr("id").split("-");

                if (ref[2] == "sku") {
                    files[ref[0]].sku = ref[1];
                    $("[id$='qty']").attr("disabled", false);
                    $("#" + ref[0] + "-" + ref[1] + "-qty").attr("disabled", true);
                } else {
                    files[ref[0]].quantity = ref[1];
                    $("[id$='sku']").attr("disabled", false);
                    $("#" + ref[0] + "-" + ref[1] + "-sku").attr("disabled", true);
                }
                checkValid()
            });
        }

        function checkValid() {
            var allValid = true;
            files.forEach(function (file, index) {
                if (file.sku && file.quantity) {
                    stockLevelFileOptions[index].setAttribute("style", "color: #1d643b; background-color: #d7f3e3;");
                } else {
                    stockLevelFileOptions[index].setAttribute("style", "color: #761b18; background-color: #f9d6d5;");
                    allValid = false;
                }
            });
            if (allValid) {
                $("#stockLevelFileUpload").attr("disabled", false);
            } else {
                $("#stockLevelFileUpload").attr("disabled", true);
            }
        }
    </script>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-hero shadow">
                <div class="card-header shadow">Operations</div>

                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            <h5>Update Stock Levels</h5>

                            <div class="form-row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <label class="input-group-prepend">
                                                    <span class="btn btn-secondary">
                                                        Choose File <input id="stockLevelsFiles" style="display: none"
                                                                           type="file" multiple accept=".csv">
                                                    </span>
                                        </label>
                                        <input type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-6" style="display:none;">
                                    <select id="stockLevelFileSelect" class="form-control"></select>
                                </div>

                            </div>
                            <div class="form-group" style="display:none;">
                                <table id="stockLevelHeaders"  class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Column Name</th>
                                        <th scope="col">SKU</th>
                                        <th scope="col">Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>


                            <div class="form-group" style="display:none;">
                                <button id="stockLevelFileUpload"  class="form-control btn btn-primary" disabled>Upload</button>
                            </div>

                        </div>
                        <div class="list-group-item">
                            <h5>Get Orders</h5>
                            <div class="form-row">
                                <div class="col-12">
                                    <button class="btn btn-primary" id="getOrders">Go</button>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <h5>Product Search</h5>
                            <div class="form-row">
                                <div class="col-8">
                                    <div class="input-group">
                                        <input id="productSearchQuery" type="text" class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" id="productSearch">Go</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <h5>Recommendations</h5>
                            <div class="form-row">
                                <div class="col-8">
                                    <div class="input-group">
                                        <button class="btn btn-primary" id="recommendationsList">Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <h5>Fulfillment Orders</h5>
                            <div class="form-row">
                                <div class="col-8">
                                    <div class="input-group">
                                        <button class="btn btn-primary" id="fulfillmentOrdersList">Go</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item">
                            <h5>Inventory Feed</h5>
                            <div class="form-row">
                                <div class="col-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <select id="inventoryReportVendor" class="form-control">
                                                <option>UNN42</option>
                                                <option>5C8M0</option>
                                                <option>VU7KE</option>
                                                <option>L4R3I</option>
                                                <option>9DB5L</option>
                                                <option>M195T</option>
                                                <option>PY0C4</option>
                                                <option>Q80DQ</option>
                                            </select>
                                        </div>
                                        <input value="100" step="10" id="inventoryReportItemCount" type="number" class="form-control">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" id="inventoryReport">Go</button>
                                        </div>
                                    </div>
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
