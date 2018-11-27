<script>
    var productAttribute;
    apiToken = "{{\Illuminate\Support\Facades\Auth::user()->api_token}}";
    $(function () {
        productAttribute = new ProductAttribute({{$productAttribute->id}});
        productAttribute.get((data) => populate(data));
        var editors = {
            title: new Editor({
                input: $("#titleField"),
                trigger: $("#titleTrigger"),
            }).triggered(function (val) {
                productAttribute.title = val;
                productAttribute.edit((data) => populate(data));
            }),
        };

        function populate(data) {
            $("#titleBreadcrumb").html(data.title);
            $("#idField").val(data.id);
            $("#titleField").val(data.title);
            ProductAttribute.show(function (attributes) {
                table.clear();
                attributes.forEach(function (attribute) {
                    table.row.add(attribute);
                });
                table.draw(false);
            });
        }

        $("#addButton").on("click", function () {
            ProductAttribute.create(function (productAttribute) {
                getUI(productAttribute.id, true);
            })
        });

        $("#deletePopover").popover({
            trigger: "click",
            boundary: "body"
        }).on("shown.bs.popover", function () {
            $("#deleteTrigger").on("click", function () {
                if ($("#deleteConfirm").val() == productAttribute.data.title) {
                    productAttribute.destroy(function () {
                        window.location = "/product-attributes";
                    })
                }
            })
        })
    })
</script>

<nav>
    <ol class="breadcrumb">
        <li id="titleBreadcrumb" class="breadcrumb-item active"></li>
        <li id="deletePopover" data-toggle="popover" data-placement="top" data-html="true" data-content="
            <div class='input-group'>
                <input id='deleteConfirm' type='text' placeholder='Type attribute title to confirm' class='form-control'>
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
            <div class="input-group mt-2">
                <div class="input-group-prepend"><span class="input-group-text">ID</span>
                </div>
                <input id="idField" disabled type="text" class="form-control">
            </div>
        </div>
        <div class="col-6">
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