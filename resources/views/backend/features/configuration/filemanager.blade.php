@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-10 col-md-10">

        <div class="card">
            <div class="card-header with-elements">
                <h5 class="card-header-title mt-1 mb-0">@lang('feature/configuration.filemanager.caption')</h5>
                <div class="card-header-elements ml-md-auto">
                </div>
            </div>
            <div class="card-body">
                <iframe src="/file-manager/fm-button" style="width: 100%; height: 780px; overflow: hidden; border: none;"></iframe>
            </div>
        </div>

    </div>
</div>
@endsection