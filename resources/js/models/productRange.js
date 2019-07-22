var ProductRange = function (attributes) {
    Model.call(this, attributes);
};

ProductRange.prototype = Object.create(Model.prototype);
ProductRange.prototype.constructor = ProductRange;
ProductRange.prototype.endpoint = "product-ranges";


ProductRange.prototype.populate = function (data) {
    Object.getPrototypeOf(ProductRange.prototype).populate(data, this);
    data.products.forEach((productData, key) => {
        this.products[key] = new Product(productData.id);
        this.products[key].populate(productData);
    });
};

// class ProductRange extends Model{
//     constructor(id){
//         super( "product-ranges", id);
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         var that = this;
//         super.get(function (data) {
//
//             onDone(data);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-ranges",attributes,  function (data) {
//             var productRange = new ProductRange(data.id);
//             productRange.populate(data);
//             onDone(productRange);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//
//     populate(data) {
//         super.populate(data);
//         this.products.forEach((productData, key) => {
//             this.products[key] = new Product(productData.id);
//             this.products[key].populate(productData);
//         });
//     }
// }