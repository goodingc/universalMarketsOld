@extends("layouts.app")

@section("head")
    <script defer>
        $(function () {
            setTable();
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
                url: `/products/${id}/view`,
                method: "GET",
                data: {
                    api_token: apiToken,
                }
            }).done(function (data) {
                $("#infoCard").find(".card-body").html(data);
                if(push) history.pushState({id: id}, null, "/products/"+id);
            })
        }
    </script>
@endsection

@section("content")

    <div class="row justify-content-center">
        <div class="col-4">
            @component("components.heroCard")
                @slot("title")
                    Search
                @endslot

                @component("components.search",[
                    "columns" => [
                        "sku" => "SKU",
                        "title" => "Title"
                    ],
                    "searchEndpoint" => "products.search",
                    "filters" => [
                        "sku" => [
                            "name" => "SKU",
                            "input" => [
                                "type" => "text"
                            ]
                        ],
                        "title" => [
                            "name" => "Title",
                            "input" => [
                                "type" => "text"
                            ]
                        ]
                    ]
                ])
                @endcomponent
            @endcomponent
        </div>
        <div class="col-7">
            @component("components.heroCard", ["id"=>"infoCard"])
                @slot("title")
                    Product
                @endslot

                @if(isset($product))
                    @component("products.view", ["product"=>$product])

                    @endcomponent
                @endif

            @endcomponent
        </div>
    </div>

@endsection