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
        super.get(function (data) {
            onDone(data);
        })
    }

    static create(onDone){
        super.create("product-ranges", function (data) {
            var productRange = new ProductRange(data.id);
            productRange.data = data;
            onDone(productRange);
        })
    }

    destroy(onDone){
        super.destroy(function (data) {
            onDone(data);
        })
    }
}