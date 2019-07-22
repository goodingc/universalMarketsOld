var ProductAttributeAssignment = function (attributes) {
    Model.call(this, attributes);
};

ProductAttributeAssignment.prototype = Object.create(Model.prototype);
ProductAttributeAssignment.prototype.constructor = ProductAttributeAssignment;
ProductAttributeAssignment.prototype.endpoint = "product-attribute-assignments";


ProductAttributeAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(ProductAttributeAssignment.prototype).populate(data, this);
    this.attribute = new ProductAttribute(data.attribute);
};

// class ProductAttributeValue extends Model{
//     constructor(){
//         super("product-attribute-values");
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
//         super.show("product-attribute-values", attributes,function (data) {
//             var productAttributeValues = [];
//             data.forEach(function (datum) {
//                 var productAttributeValue = new ProductAttribute();
//                 productAttributeValue.populate(datum);
//                 productAttributeValues.push(productAttributeValue);
//             });
//             onDone(productAttributeValues);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-attribute-values",attributes, function (data) {
//             var productAttributeValue = new ProductAttributeValue();
//             productAttributeValue.populate(data);
//             onDone(productAttributeValue);
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
//         var attribute = new ProductAttribute(data.attribute.id);
//         attribute.populate(data.attribute);
//         this.attribute = attribute;
//     }
// }