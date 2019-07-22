var ProductBlock = function (attributes) {
    Model.call(this, attributes);
};

ProductBlock.prototype = Object.create(Model.prototype);
ProductBlock.prototype.constructor = ProductBlock;
ProductBlock.prototype.endpoint = "product-blocks";


// class ProductBlock extends Model{
//     constructor(){
//         super("product-blocks");
//     }
//
//     edit(onDone){
//         super.edit(function (data) {
//             onDone(data);
//         })
//     }
//
//     static show(attributes, onDone){
//         super.show("product-blocks", attributes, function (data) {
//             var productBlocks = [];
//             data.forEach(function (datum) {
//                 var productBlock = new ProductBlock();
//                 productBlock.populate(datum);
//                 productBlocks.push(productBlock);
//             });
//             onDone(productBlocks);
//         });
//     }
//
//     static reasons(onDone){
//         $.ajax({
//             url: `/api/product-blocks/reasons`,
//             method: "GET",
//             data: {
//                 api_token: apiToken,
//             },
//         }).done(function (data) {
//             onDone(data);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("product-blocks", attributes,function (data) {
//             var productBlock = new ProductBlock();
//             productBlock.populate(data);
//             onDone(productBlock);
//         })
//     }
//
//     destroy(onDone){
//         super.destroy(function (data) {
//             onDone(data);
//         })
//     }
//}