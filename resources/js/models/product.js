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
        super.get(function (data) {
            onDone(data);
        })
    }

    static create(onDone){
        super.create("products", function (data) {
            var product = new Product(data.id);
            product.data = data;
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
            that.data = data;
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
            that.data = data;
            onDone(data);
        })
    }
}