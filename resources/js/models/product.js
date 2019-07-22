var Product = function (attributes) {
    Model.call(this, attributes);
};

Product.prototype = Object.create(Model.prototype);
Product.prototype.constructor = Product;
Product.prototype.endpoint = "products";

Product.prototype.populate = function (data) {
    Object.getPrototypeOf(Product.prototype).populate(data, this);
    if(data.attribute_assignments)this.attribute_assignments = data.attribute_assignments.map((data) => new ProductAttributeAssignment(data));
    if(data.barcodes)this.barcodes = data.barcodes.map((data) => new ProductBarcode(data));
    if(data.blocks)this.blocks = data.blocks.map((data) => new ProductBlock(data));
    if(data.inventory_bay_assignments)this.inventory_bay_assignments = data.inventory_bay_assignments.map((data) => new InventoryBayAssignment(data));
    if(data.sales_channel_assignments)this.sales_channel_assignments = data.sales_channel_assignments.map((data) => new SalesChannelAssignment(data));
    if(data.child_groups)this.child_groups = data.child_groups.map((data) => new ProductGroup(data));
};

Product.prototype.addAttribute = function(attribute, value, onDone){
    ProductAttributeAssignment.prototype.create({
        product_id: this.id,
        product_attribute_id: attribute,
        value: value
    }, (assignment)=>{
        this.attribute_assignments.push(assignment);
        onDone(assignment);
    })
};

Product.prototype.removeAttribute = function(attribute, onDone){
    this.attribute_assignments.forEach((value, key)=>{
        if(value.attribute.id == attribute){
            value.destroy((data)=>{
                this.attribute_assignments.splice(key, 1);
                onDone(data)
            });
        }
    });

};

Product.prototype.editAttribute = function(attribute, newValue, onDone){
    this.attribute_assignments.forEach((value)=>{
        if(value.attribute.id == attribute){
            value.value = newValue;
            value.edit((data)=>onDone(data));
        }
    });
};

Product.prototype.addBlock = function(reason, salesChannel, onDone){
    ProductBlock.prototype.create({
        product_id: this.id,
        reason_id: reason,
        sales_channel_id: salesChannel,
    }, (block)=>{
        this.blocks.push(block);
        onDone(block);
    })
};

Product.prototype.removeBlock = function(reason, salesChannel, onDone){
    this.blocks.forEach((block, key)=>{
        if(block.reason_id == reason && block.sales_channel_id == salesChannel){
            block.destroy((data)=>{
                this.blocks.splice(key, 1);
                onDone(data)
            })
        }
    });
};

Product.prototype.addInventoryBayAssignment = function(bay, quantity, onDone){
    InventoryBayAssignment.prototype.create({
        inventory_bay_id: bay,
        product_id: this.id,
        quantity: quantity,
    }, (assignment)=>{
        this.inventory_bay_assignments.push(assignment);
        onDone(assignment);
    })
};

Product.prototype.removeInventoryBayAssignment = function(bayID, onDone){
    this.inventory_bay_assignments.forEach((bay, key)=>{
        if(bay.inventory_bay_id == bayID){
            bay.destroy((data)=>{
                this.inventory_bay_assignments.splice(key, 1);
                onDone(data)
            })
        }
    });
};

Product.prototype.editInventoryBayAssignment = function(bayID, quantity, onDone){
    this.inventory_bay_assignments.forEach((bay)=>{
        if(bay.inventory_bay_id == bayID){
            bay.quantity = quantity;
            bay.edit((data)=>{onDone(data)})
        }
    });
};

Product.prototype.addBarcode = function(barcode, quantity, onDone){
    ProductBarcode.prototype.create({
        id: barcode,
        quantity: quantity,
        product_id: this.id,
    }, (barcode)=>{
        this.barcodes.push(barcode);
        onDone(barcode);
    })
};

Product.prototype.removeBarcode = function(barcode, onDone){
    for (let i = 0; i < this.barcodes.length; i++) {
        var barcodeC = this.barcodes[i];
        if(barcodeC.id == barcode){
            barcodeC.destroy((data)=>{
                this.barcodes.splice(i, 1);
                onDone(data);
            });
            return;
        }
    }
};

Product.prototype.editBarcode = function(barcode, quantity, onDone){
    for (let i = 0; i < this.barcodes.length; i++) {
        var barcodeC = this.barcodes[i];
        if(barcodeC.id == barcode){
            this.barcodes[i].quantity = quantity;
            this.barcodes[i].edit((data)=>onDone(data));
            return;
        }
    }

};

Product.prototype.addGroup = function(group, quantity, onDone){
    ProductGroup.prototype.create({
        product_id: group,
        parent_id: this.id,
        quantity: quantity,
    }, (group)=>{
        this.child_groups.push(group);
        onDone(group);
    })
};

Product.prototype.removeGroup = function(groupID, onDone){
    this.child_groups.forEach((group, key)=>{
        if(group.product_id == groupID){
            group.destroy((data)=>{
                this.child_groups.splice(key, 1);
                onDone(data);
            })
        }
    });
};

Product.prototype.editGroup = function(groupID, quantity, onDone){
    this.child_groups.forEach((group)=>{
        if(group.product_id == groupID){
            group.quantity = quantity;
            group.edit((data)=>{onDone(data)})
        }
    });
};



// class Product extends Model{
//     constructor(id){
//         super("products", id);
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
//     static create(attributes, onDone){
//         super.create("products", attributes, function (data) {
//             var product = new Product(data.id);
//             product.populate(data);
//             onDone(product);
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
//         var newAttributeVals = [];
//         data.attribute_values.forEach((valData) => {
//             newAttributeVals[valData.product_attribute_id] = new ProductAttributeValue();
//             newAttributeVals[valData.product_attribute_id].populate(valData);
//         });
//         this.attribute_values = newAttributeVals;
//         data.barcodes.forEach((barcodeData, key) => {
//             this.barcodes[key] = new ProductBarcode(barcodeData.id);
//             this.barcodes[key].populate(barcodeData);
//         });
//         data.blocks.forEach((blockData, key) => {
//             this.blocks[key] = new ProductBlock();
//             this.blocks[key].populate(blockData);
//         });
//         var newAssignments = [];
//         data.inventory_bay_assignments.forEach((assignmentData, key)=>{
//             newAssignments[assignmentData.bay.id] = new InventoryBayAssignment();
//             newAssignments[assignmentData.bay.id].populate(assignmentData);
//         });
//         this.inventory_bay_assignments = newAssignments;
//         var newSalesChannels = [];
//         data.sales_channels.forEach((scData)=>{
//             newSalesChannels[scData.id] = scData;
//         });
//         this.sales_channels = newSalesChannels;
//         var newChildGroups = [];
//         data.child_groups.forEach((groupData)=>{
//             newChildGroups[groupData.product_id] = new ProductGroup();
//             newChildGroups[groupData.product_id].populate(groupData);
//         });
//         this.child_groups = newChildGroups;
//     }
//
//     addAttribute(attribute, value, onDone){
//         ProductAttributeValue.create({
//             product_id: this.id,
//             product_attribute_id: attribute,
//             value: value
//         }, (attrVal)=>{
//             this.attribute_values[attribute] = attrVal;
//             onDone(attrVal);
//         })
//     }
//
//     removeAttribute(attribute, onDone){
//         this.attribute_values[attribute].destroy((data)=>{
//             this.attribute_values[attribute] = null;
//             onDone(data)
//         });
//     }
//
//     editAttribute(attribute, newValue, onDone){
//         this.attribute_values[attribute].value = newValue;
//         this.attribute_values[attribute].edit((data)=>onDone(data));
//     }
//
//     addBlock(reason, salesChannel, onDone){
//         ProductBlock.create({
//             product_id: this.id,
//             reason_id: reason,
//             sales_channel_id: salesChannel,
//         }, (block)=>{
//             this.blocks.push(block);
//             onDone(block);
//         })
//     }
//
//     removeBlock(reason, salesChannel, onDone){
//         this.blocks.forEach((block, key)=>{
//             if(block.reason_id == reason && block.sales_channel_id == salesChannel){
//                 block.destroy((data)=>{
//                     this.blocks.splice(key, 1);
//                     onDone(data)
//                 })
//             }
//         });
//     }
//
//     addInventoryBayAssignment(bay, quantity, onDone){
//         InventoryBayAssignment.create({
//             inventory_bay_id: bay,
//             product_id: this.id,
//             quantity: quantity,
//         }, (assignment)=>{
//             this.inventory_bay_assignments[assignment.bay.id] = assignment;
//             onDone(assignment);
//         })
//     }
//
//     removeInventoryBayAssignment(bay, onDone){
//         this.inventory_bay_assignments[bay].destroy((data)=>{
//             this.inventory_bay_assignments.splice(bay, 1);
//             onDone(data);
//         })
//     }
//
//     editInventoryBayAssignment(bay, quantity, onDone){
//         this.inventory_bay_assignments[bay].quantity = quantity;
//         this.inventory_bay_assignments[bay].edit((data)=>onDone(data));
//     }
//
//     addBarcode(barcode, quantity, onDone){
//         ProductBarcode.create({
//             id: barcode,
//             quantity: quantity,
//             product_id: this.id,
//         }, (barcode)=>{
//             this.barcodes.push(barcode);
//             onDone(barcode);
//         })
//     }
//
//     removeBarcode(barcode, onDone){
//         for (let i = 0; i < this.barcodes.length; i++) {
//             var barcodeC = this.barcodes[i];
//             if(barcodeC.id == barcode){
//                 barcodeC.destroy((data)=>{
//                     this.barcodes.splice(i, 1);
//                     onDone(data);
//                 });
//                 return;
//             }
//         }
//     }
//
//     editBarcode(barcode, quantity, onDone){
//         for (let i = 0; i < this.barcodes.length; i++) {
//             var barcodeC = this.barcodes[i];
//             if(barcodeC.id == barcode){
//                 this.barcodes[i].quantity = quantity;
//                 this.barcodes[i].edit((data)=>onDone(data));
//                 return;
//             }
//         }
//
//     }
//
//     addGroup(group, quantity, onDone){
//         ProductGroup.create({
//             product_id: group,
//             parent_id: this.id,
//             quantity: quantity,
//         }, (group)=>{
//             this.child_groups[group.product.id] = group;
//             onDone(group);
//         })
//     }
//
//     removeGroup(group, onDone){
//         this.child_groups[group].destroy((data)=>{
//             this.child_groups.splice(group, 1);
//             onDone(data);
//         })
//     }
//
//     editGroup(group, quantity, onDone){
//         this.child_groups[group].quantity = quantity;
//         this.child_groups[group].edit((data)=>onDone(data));
//     }
// }