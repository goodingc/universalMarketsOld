var ProductAttribute = function (attributes) {
    Model.call(this, attributes);
};

ProductAttribute.prototype = Object.create(Model.prototype);
ProductAttribute.prototype.constructor = ProductAttribute;
ProductAttribute.prototype.endpoint = "product-attributes";


// class ProductAttribute extends Model{
//     constructor(id){
//         super("product-attributes", id);
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     get(onDone){
//         super.get(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-attributes", attributes,function (data) {
//             var productAttributes = [];
//             data.forEach(function (datum) {
//                 var productAttribute = new ProductAttribute(datum.id);
//                 productAttribute.populate(datum);
//                 productAttributes.push(productAttribute);
//             });
//             onDone(productAttributes);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-attributes",attributes, function (data) {
//             var productAttribute = new ProductAttribute(data.id);
//             productAttribute.populate(data);
//             onDone(productAttribute);
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
//     }
// }