class InventoryBay extends Model{
    constructor(id){
        super(id, "inventory-bays");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        super.get(function (data) {
            onDone(data);
        })
    }

    static show(onDone){
        $.ajax({
            url: `/api/product-attributes`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            var productAttributes = [];
            data.forEach(function (datum) {
                var productAttribute = new ProductAttribute(datum.id);
                productAttribute.data = datum;
                productAttributes.push(productAttribute);
            });
            onDone(productAttributes);
        })
    }

    static create(onDone){
        super.create("product-attributes", function (data) {
            var productAttribute = new ProductAttribute(data.id);
            productAttribute.data = data;
            onDone(productAttribute);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}