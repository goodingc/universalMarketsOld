@extends("layouts.app")

@section("content")
    <div class="row justify-content-center">
        <div class="col-3">
            <script>
                $(function () {
                    Product.prototype.attributes((attributes)=>{
                        $("#attributeTypeSelect").html(
                            attributes.map(
                                attribute=>`<option value="${attribute}">${attribute.split("_").map((attributeWord)=>attributeWord.replace(/^\w/, c => c.toUpperCase())).join(" ")}</option>`
                            ).join("")
                        );
                    })
                })
            </script>
            @component("components.heroCard")
                @slot("title")
                    Search
                @endslot

                <div class="row">
                    <div class="col-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <select id="attributeTypeSelect" class="form-control"></select>
                            </div>
                            <input type="text" class="form-control" id="attributeFilterContent">
                            <div class="input-group-append">
                                <button class="btn" id="attributeFilterAdd">Add</button>
                            </div>
                        </div>
                    </div>
                </div>

            @endcomponent
        </div>
    </div>
@endsection