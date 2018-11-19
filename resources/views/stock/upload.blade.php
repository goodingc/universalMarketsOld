@extends("layouts.app")

@section("head")
    <script defer>
        var stockLevelFileUpload;
        $(function () {
            stockLevelFileUpload = new StockFileUploader({
                fileInput: $("#stockLevelFiles"),
                statusOutput: $("#stockLevelFileOutput"),
                uploadTrigger: $("#stockLevelFileUpload"),
                fileSelect: $("#stockLevelFileSelect"),
                headerTable: $("#stockLevelHeaders"),
                progressList: $("#progressList"),
            }).progressed(function () {

            });

            stockLevelFileUpload.uploadTrigger.on("click",function () {
                stockLevelFileUpload.upload();
            })
        });
    </script>
@endsection

@section("content")
    <div class="row justify-content-center">
        <div class="container">
            <div class="card card-hero shadow">
                <div class="card-header shadow">
                    Upload Stock File
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="form-row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <label class="input-group-prepend">
                                    <span class="btn btn-secondary">
                                        Choose File <input id="stockLevelFiles" style="display: none" type="file" multiple accept=".csv">
                                    </span>
                                        </label>
                                        <input id="stockLevelFileOutput" type="text" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-6" style="display:none;">
                                    <select id="stockLevelFileSelect" class="form-control"></select>
                                </div>

                            </div>
                            <div class="form-group" style="display:none;">
                                <table id="stockLevelHeaders" class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Column Name</th>
                                        <th scope="col">SKU</th>
                                        <th scope="col">Quantity</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>


                            <div class="form-group" style="display:none;">
                                <button id="stockLevelFileUpload" class="form-control btn btn-primary" disabled>Upload</button>
                            </div>
                        </div>
                        <div class="col-4">
                            <ul class="list-group" id="progressList">

                            </ul>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection