class Model {
    constructor(id, endpoint) {
        this.id = id;
        this.endpoint = endpoint;
    }

    edit(onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "POST",
            data: {
                api_token: apiToken,
                data: this.data,
            }
        }).done(function (data) {
            that.data = data;
            onDone(data);
        })
    }

    get(onDone){
        var that = this;
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            that.data = data;
            onDone(data);
        });
    }

    static create(endpoint, onDone){
        $.ajax({
            url: `/api/${endpoint}/create`,
            method: "GET",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            onDone(data);
        })
    }

    destroy(onDone){
        $.ajax({
            url: `/api/${this.endpoint}/${this.id}`,
            method: "DELETE",
            data: {
                api_token: apiToken,
            }
        }).done(function (data) {
            onDone(data);
        })
    }

}