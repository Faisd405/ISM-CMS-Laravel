@extends('layouts.backend.layout')

@section('content')
<h4 class="font-weight-bold py-3 mb-3">
    @lang('module/dashboard.caption')
    <div class="text-muted text-tiny mt-1 time-frame">
        <small class="font-weight-normal">@lang('module/dashboard.today_caption') {{ now()->isoFormat('dddd, D MMMM Y') }} (<em id="timenow"></em>)</small>
    </div>
</h4>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-primary alert-dismissible fade show text-muted">
            <i class="las la-user"></i>  @lang('module/dashboard.welcome_caption') <strong><em>{!! Auth::user()['name'] !!}</em></strong> !
        </div>
    </div>
</div>

@if ($data['maintenance'] == 1)
<div class="alert alert-warning alert-dismissible fade show">
  <i class="las la-tools" style="font-size: 1.3em;"></i>
  @lang('module/dashboard.maintenance_caption')
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/moment/moment.js') }}"></script>
@endsection

@section('jsbody')
<script>
    $(document).ready(function() {
        var interval = setInterval(function() {
            var momentNow = moment();
            $('#timenow').html(momentNow.format('hh:mm:ss A'));
        }, 100);
    });
</script>
@endsection
