class ProductBarcode extends Model{
    constructor(id){
        super(id, "product-barcodes");
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
            url: `/api/product-barcodes`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            var productBarcodes = [];
            data.forEach(function (datum) {
                var productBarcode = new ProductBarcode(datum.id);
                productBarcode.populate(datum);
                productBarcodes.push(barcode);
            });
            onDone(productBarcodes);
        })
    }

    static create(onDone){
        super.create("product-barcodes", function (data) {
            var productBarcode = new ProductBarcode(data.id);
            productBarcode.populate(datum);
            onDone(barcode);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}