var InventoryBayAssignment = function (attributes) {
    Model.call(this, attributes);
};

InventoryBayAssignment.prototype = Object.create(Model.prototype);
InventoryBayAssignment.prototype.constructor = InventoryBayAssignment;
InventoryBayAssignment.prototype.endpoint = "inventory-bay/assignments";


InventoryBayAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(InventoryBayAssignment.prototype).populate(data, this);
    if(data.bay)this.bay = new InventoryBay(data.bay);
    if(data.product)this.product = new Product(data.product);
};

// class InventoryBayAssignment extends Model{
//     constructor(){
//         super("inventory-bay-assignments");
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
//         super.show("inventory-bay-assignments", attributes,function (data) {
//             var assignments = [];
//             data.forEach(function (datum) {
//                 var assignment = new InventoryBayAssignment();
//                 assignment.populate(datum);
//                 assignments.push(assignment);
//             });
//             onDone(assignments);
//         })
//     }
//
//     static create(attributes, onDone){
//         super.create("inventory-bay-assignments",attributes, function (data) {
//             var assignment = new InventoryBayAssignment();
//             assignment.populate(data);
//             onDone(assignment);
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
//         var inventoryBay = new InventoryBay(data.bay.id);
//         inventoryBay.populate(data.bay);
//         this.bay = inventoryBay;
//     }
// }