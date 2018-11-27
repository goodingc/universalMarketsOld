class Product extends Model{
    constructor(id){
        super(id, "products");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        super.get(function (data) {
            that.product_attributes.forEach(function (attributeData, key) {
                that.product_attributes[key] = new ProductAttribute(attributeData.id);
                that.product_attributes[key].populate(attributeData);
            });
            that.barcodes.forEach(function (barcodeData, key) {
                that.barcodes[key] = new ProductBarcode(barcodeData.id);
                that.barcodes[key].populate(barcodeData);
            });
            onDone(data);
        })
    }

    static create(onDone){
        super.create("products", function (data) {
            var product = new Product(data.id);
            product.populate(data);
            onDone(product);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }

    addAttribute(data, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/add`,
            method: "POST",
            data: {
                api_token: apiToken,
                data: data,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    removeAttribute(id, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/${id}`,
            method: "DELETE",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    editAttribute(id, value, onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}/attributes/${id}`,
            method: "POST",
            data: {
                api_token: apiToken,
                data:{
                    value: value
                }
            }
        }).done(function (data) {
            that.populate(data);
            onDone(data);
        })
    }

    addBarcode(){

    }
}