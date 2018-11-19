class Editor{
    constructor(params){
        this.trigger = params.trigger;
        this.input = params.input;
        this.editing = false;
        var that = this;

        this.trigger.on("click", function () {
            if(!that.editing){
                that.input.attr("disabled", false);
                that.trigger.html("Save");
                that.editing = true;
            }else{
                that.input.attr("disabled", true);
                that.trigger.html("Edit");
                that.editing = false;
                that.onTriggered(that.input.val());
            }
        })
    }

    triggered(f){
        this.onTriggered = f;
        return this;
    }
}