<script>
    var productRange;
    apiToken = "{{\Illuminate\Support\Facades\Auth::user()->api_token}}";
    $(function () {
        var productTable = $("#productTable").DataTable({
            paging:false,
            searching: false,
            responsive: true,
            scrollY: $(".card-hero > .card-body").height()-$(".breadcrumb").height()-145,
            columns: [
                {data: "id"},
                {data: "sku"},
                {data: "title"},
            ],
        });

        productTable.to$().find("tbody tr").on('click', function () {
            window.location = $(this).attr("href");
        });

        productRange = new ProductRange({{$productRange->id}});
        productRange.get((data) => populate(data));

        function populate(data){
            $("#idField").val(data.id);
            $("#skuField").val(data.sku);
            $("#skuBreadcrumb").html(data.sku);
            $("#titleField").val(data.title);
            productTable.clear();
            data.products.forEach(function (product) {
                $(productTable.row.add(product).node()).on("click", function () {
                    window.location = `/products/${product.id}`;
                })
            });
            productTable.draw().columns.adjust();
        }

        var editors = {
            sku: new Editor({
                input: $("#skuField"),
                trigger: $("#skuTrigger"),
            }).triggered(function (val) {
                productRange.data.sku = val;
                productRange.edit((data) => populate(data));
            }),
            title: new Editor({
                input: $("#titleField"),
                trigger: $("#titleTrigger"),
            }).triggered(function (val) {
                productRange.data.title = val;
                productRange.edit((data) => populate(data));
            }),
        };

        $("#addButton").on("click", function () {
            ProductRange.create(function (productRange) {
                getUI(productRange.id, true);
            })
        });

        $("#deletePopover").popover({
            trigger: "click",
            boundary: "body"
        }).on("shown.bs.popover", function () {
            $("#deleteTrigger").on("click", function () {
                if($("#deleteConfirm").val() == productRange.data.sku){
                    productRange.destroy(function () {
                        window.location = "/product-ranges";
                    })
                }
            })
        })
    })
</script>

<nav>
    <ol class="breadcrumb">
        <li id="skuBreadcrumb" class="breadcrumb-item"></li>
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
    <div class="col-6">
        <div class="row">
            <div class="col-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">ID</span>
                    </div>
                    <input id="idField" disabled type="text" class="form-control">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">SKU</span>
                    </div>
                    <input disabled id="skuField" type="text" class="form-control">
                    <div class="input-group-append">
                        <button id="skuTrigger" class="btn">Edit</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Title</span>
                    </div>
                    <input id="titleField" disabled type="text" class="form-control">
                    <div class="input-group-append">
                        <button id="titleTrigger" class="btn">Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <table id="productTable" class="table table-striped table-hover">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">SKU</th>
                <th scope="col">Title</th>
            </tr>
            </thead>
            <tbody>
            {{--@foreach($productRange->products as $product)--}}
                {{--<tr href="/products/{{$product->id}}">--}}
                    {{--<th scope="row">{{$product->id}}</th>--}}
                    {{--<td>{{$product->sku}}</td>--}}
                    {{--<td>{{$product->title}}</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
            </tbody>
        </table>
    </div>
</div>