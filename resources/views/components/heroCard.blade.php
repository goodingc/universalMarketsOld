<div {{isset($id)?"id=".$id:""}} class="card card-hero shadow">
    <div class="card-header shadow">
        {{$title}}
    </div>
    <div class="card-body">
        {{$slot}}
    </div>
</div>