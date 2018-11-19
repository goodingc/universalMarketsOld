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
                url: `/product-ranges/${id}/view`,
                method: "GET",
                data: {
                    api_token: apiToken,
                }
            }).done(function (data) {
                $("#infoCard").find(".card-body").html(data);
                if(push) history.pushState({id: id}, null, "/product-ranges/"+id);
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
                        "id" => "ID",
                        "sku" => "SKU",
                        "title" => "Title",
                    ],
                    "searchEndpoint" => "productRanges.search",
                    "filters" => [
          //              "id" => [
          //                  "name" => "ID",
          //                  "input" => [
          //                      "type" => "number",
          //                  ]
          //              ],
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
                    Product Range
                @endslot

                @if(isset($productRange))
                    @component("productRanges.view", ["productRange"=>$productRange])

                    @endcomponent
                @endif

            @endcomponent
        </div>
    </div>

@endsection