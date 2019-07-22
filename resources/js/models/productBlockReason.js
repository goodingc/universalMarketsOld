var ProductBlockReason = function (attributes) {
    Model.call(this, attributes);
};

ProductBlockReason.prototype = Object.create(Model.prototype);
ProductBlockReason.prototype.constructor = ProductBlockReason;
ProductBlockReason.prototype.endpoint = "product-block-reasons";
