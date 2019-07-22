var ProductGroup = function (attributes) {
    Model.call(this, attributes);
};

ProductGroup.prototype = Object.create(Model.prototype);
ProductGroup.prototype.constructor = ProductGroup;
ProductGroup.prototype.endpoint = "product-groups";


// class ProductGroup extends Model{
//     constructor(){
//         super("product-groups");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-groups", attributes, function (data) {
//             var productGroups = [];
//             data.forEach(function (datum) {
//                 var productGroup = new ProductGroup();
//                 productGroup.populate(datum);
//                 productGroups.push(productGroup);
//             });
//             onDone(productGroups);
//         });
//     }
//
//     static create(attributes, onDone){
//         super.create("product-groups", attributes,function (data) {
//             var productGroup = new ProductGroup();
//             productGroup.populate(data);
//             onDone(productGroup);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
// }