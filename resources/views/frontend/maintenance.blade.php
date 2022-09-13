@extends('layouts.frontend.layout-maintenance')

@section('content')
<div class="layout-content">

    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

      <div class="container px-0">
        <h2 class="text-center font-weight-bolder pt-5">
            <i class="lnr lnr-construction d-block"></i> @lang('global.maintenance.text')
        </h2>
        <div class="text-center text-muted text-big mx-auto mt-3" style="max-width: 500px;">
            @lang('global.maintenance.desc')

        </div>
        <div class="text-center mt-4">
          <h1 class="font-weight-bolder">{!! config('cmsConfig.general.website_name') !!}</h1>
        </div>
      </div>

    </div>
    <!-- / Content -->

</div>
@endsection