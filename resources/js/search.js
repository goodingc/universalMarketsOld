class Search {
    constructor(params){
        var that = this;
        this.filterTypeSelect = params.filterTypeSelect;
        this.filterContent = params.filterContent;
        this.filterAdd = params.filterAdd;
        this.filterTypes = params.filterTypes;

        this.filters = [];

        for(var filterType in this.filterTypes){
            this.filterTypeSelect.append(new Option(this.filterTypes[filterType].name, filterType));
        }

        this.filterTypeSelect.on("change", function () {
            var id = that.filterContent.attr("id");
            that.filterContent.replaceWith(ProductSearch.inputType(that.filterTypes[$(this).val()].input));
            that.filterContent = $("#"+id);
        });

        this.filterAdd.on("click", function () {
            var filter = {
                id: (Math.random()*0xFFFFFF<<0).toString(16),
                type: that.filterTypeSelect.val(),
                content: that.filterContent.val(),
            };
            that.filters.push(filter);
            that.onFilterAdd(filter);
        });
    }

    addFilter(f){
        this.onFilterAdd = f;
        return this;
    }

    removeFilter(f){
        this.onFilterRemove = f;
        return this;
    }

    filterRemove(filterID){
        var that = this;
        this.filters.forEach(function (filter, index) {
            if(filterID == filter.id){
                that.filters.splice(index, 1);
                that.onFilterRemove();
            }
        })
    }

    getFilterName(filterType){
        return this.filterTypes[filterType].name;
    }

    static filterBadge(filterName, filterContent, filterID){
        return "<div class='btn-group mb-1 mr-1'><div class='btn btn-outline-primary disabled px-1 py-0'>"+
            filterName+": "+ filterContent+
            "</div><div id='filterRemove-"+filterID+"' class='btn btn-outline-danger px-1 py-0'>×</div></div>";
    }

    static inputType(params){
        switch (params.type) {
            case "text":
                return "<input class='form-control' id='filterContent' type='text'>";
            case "select":
                var output = "<select id='filterContent' class='custom-select'>";
                for(var option in params.options){
                    output += "<option value='"+option+"'>"+params.options[option]+"</option>";
                }
                output += "</select>";
                return output;
            case "number":
                return "<input id='filterContent' type='number' class='form-control'>";
        }
        return "<div id='filterContent' class='form-control'>invalid input type!</div>";
    }
}