var InventoryBay = function (attributes) {
    Model.call(this, attributes);
};

InventoryBay.prototype = Object.create(Model.prototype);
InventoryBay.prototype.constructor = InventoryBay;
InventoryBay.prototype.endpoint = "inventory-bays";