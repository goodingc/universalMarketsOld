var ProductBarcode = function (attributes) {
    Model.call(this, attributes);
};

ProductBarcode.prototype = Object.create(Model.prototype);
ProductBarcode.prototype.constructor = ProductBarcode;
ProductBarcode.prototype.endpoint = "product-barcodes";



// class ProductBarcode extends Model{
// //     constructor(id){
// //         super("product-barcodes", id);
// //     }
// //
// //     edit(onDone){
// //         super.edit(function (data) {
// //             onDone(data);
// //         })
// //     }
// //
// //     get(onDone){
// //         super.get(function (data) {
// //             onDone(data);
// //         })
// //     }
// //
// //     static show(attributes, onDone){
// //         super.show("product-barcodes", attributes, function (data) {
// //             var productBarcodes = [];
// //             data.forEach(function (datum) {
// //                 var productBarcode = new ProductBarcode(datum.id);
// //                 productBarcode.populate(datum);
// //                 productBarcodes.push(productBarcode);
// //             });
// //             onDone(productBarcodes);
// //         })
// //     }
// //
// //     static create(attributes, onDone){
// //         super.create("product-barcodes",attributes, function (data) {
// //             var barcode = new ProductBarcode(data.id);
// //             barcode.populate(data);
// //             onDone(barcode);
// //         })
// //     }
// //
// //     destroy(onDone){
// //         super.destroy(function (data) {
// //             onDone(data);
// //         })
// //     }
// // }