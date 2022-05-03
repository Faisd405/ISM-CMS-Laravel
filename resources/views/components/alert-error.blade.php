@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="las la-thumbtack" style="font-size: 1.2em;"></i> {{ $error }}
    </div>
    @endforeach
@endif