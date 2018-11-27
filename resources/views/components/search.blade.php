<script defer>
    var productSearch;
    var table;
    $(function () {
        productSearch = new Search({
            filterTypeSelect: $("#filterType"),
            filterContent: $("#filterContent"),
            filterAdd: $("#applyFilter"),
            filterTypes: @json($filters),
        }).addFilter(function (filter) {
            $("#filterList").empty();
            productSearch.filters.forEach(function (filter) {
                $("#filterList").prepend(ProductSearch.filterBadge(productSearch.getFilterName(filter.type), filter.content, filter.id));
            });
            $("[id^=filterRemove-]").on("click", function () {
                productSearch.filterRemove($(this).attr("id").split("-")[1]);
            });
            $.ajax({
                url: "{{route($searchEndpoint)}}",
                method: "GET",
                data: {
                    filters: productSearch.filters,
                    api_token: apiToken,
                }
            }).done(function (data) {
                table.clear();
                table.rows.add(data).draw(false);
                table.columns.adjust()
            })
        }).removeFilter(function () {
            productSearch.onFilterAdd(null);
        });

        //setTable();


    });

    let prevHeight = $(window).height();
    $( window ).resize(function() {
        if(prevHeight != $(window).height()){
            table.destroy();
            setTable();
            prevHeight = $(window).height();
        }
    });

    function setTable() {
        table = $("#searchTable").DataTable({
            scrollY: $(".card-hero > .card-body").height()-$("#searchBarRow").height()-$("#filterBadgeRow").height()-180,
            //paging: false,
            searching: false,
            info: false,
            select: {
                style: "single"
            },
            columns: [
                @foreach($columns as $colName => $_)
                    {data: "{{$colName}}"},
                @endforeach
            ]

        });
    }

</script>

<div id="searchBarRow" class="row mb-2">
    <div class="col-12">
        <div class="input-group">
            <div class="input-group-prepend">
                <select id="filterType" class="custom-select"></select>
            </div>
            <input class="form-control" id="filterContent" type='text'>
            <div class="input-group-append">
                <button id="applyFilter" class="btn btn-primary">Add</button>
            </div>
        </div>
    </div>
</div>
<div id="filterBadgeRow" class="row mb-2">
    <div class="col-12" id="filterList">
    </div>
</div>

<table id="searchTable" class="table display pageResize">
    <thead>
    <tr>
        @foreach($columns as $column)
            <th>{{$column}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>