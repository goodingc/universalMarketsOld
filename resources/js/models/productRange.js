class ProductRange extends Model{
    constructor(id){
        super(id, "product-ranges");
    }

    edit(onDone){
        super.edit(function (data) {
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        super.get(function (data) {
            that.products.forEach(function (productData, key) {
                that.products[key] = new Product(productData.id);
                that.products[key].populate(productData);
            })
            onDone(data);
        })
    }

    static create(onDone){
        super.create("product-ranges", function (data) {
            var productRange = new ProductRange(data.id);
            productRange.populate(data);
            onDone(productRange);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}