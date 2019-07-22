var SalesChannelAssignment = function (attributes) {
    Model.call(this, attributes);
};

SalesChannelAssignment.prototype = Object.create(Model.prototype);
SalesChannelAssignment.prototype.constructor = SalesChannelAssignment;
SalesChannelAssignment.prototype.endpoint = "sales-channel-assignments";

SalesChannelAssignment.prototype.populate = function (data) {
    Object.getPrototypeOf(SalesChannelAssignment.prototype).populate(data, this);
    // this.sales_channel = new SalesChannel(data.bay);
};