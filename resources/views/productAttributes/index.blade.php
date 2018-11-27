@extends("layouts.app")

@section("head")
    <script defer>
        var table;
        $(function () {
            table = $("#productAttributeTable").DataTable({
                scrollY: $(".card-hero > .card-body").height()-80,
                info: false,
                paging: false,
                searching:false,
                select: {
                    style: "single"
                },
                columns: [
                    {data: "id"},
                    {data: "title"},
                ]
            });

            ProductAttribute.show(function (attributes) {
                attributes.forEach(function (attribute) {
                    table.row.add(attribute);
                });
                table.draw(false);
            });

            table.on("select", function (e, dt, type, index) {
                var selectedData = table.row(index).data();
                getUI(selectedData.id, true);
            });

            window.onpopstate = function (event) {
                getUI(event.state.id);
            };
        });

        function getUI(id, push = false) {
            $.ajax({
                url: `/product-attributes/${id}/view`,
                method: "GET",
                data: {
                    api_token: apiToken,
                }
            }).done(function (data) {
                $("#infoCard").find(".card-body").html(data);
                if(push) history.pushState({id: id}, null, "/product-attributes/"+id);
            })
        }
    </script>
@endsection

@section("content")

    <div class="row justify-content-center">
        <div class="col-4">
            @component("components.heroCard")
                @slot("title")
                    Product Attributes
                @endslot

                <table id="productAttributeTable" class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            @endcomponent
        </div>
        <div class="col-7">
            @component("components.heroCard", ["id"=>"infoCard"])
                @slot("title")
                    Product Attribute
                @endslot

                @if(isset($productAttribute))
                    @component("productAttributes.view", ["productAttribute"=>$productAttribute])

                    @endcomponent
                @endif

            @endcomponent
        </div>
    </div>

@endsection