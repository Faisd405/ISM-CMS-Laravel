@extends('layouts.backend.layout')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/backend/vendor/css/pages/account.css') }}">
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-10 col-md-10">

        @if (config('cms.module.feature.language.multiple') == true)
        <ul class="nav nav-tabs mb-4">
            @foreach ($data['languages'] as $item)
            <li class="nav-item">
                <a class="nav-link {{ $item['iso_codes'] == Request::segment(4) ? 'active' : '' }}"  href="{{ route('configuration.text', ['lang' => $item['iso_codes']]) }}">
                    {{ Str::upper($item['name']) }}
                </a>
            </li>
            @endforeach
        </ul>
        @endif

        <form action="" method="GET">
            @csrf
            <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-top-responsive-link-1">
                    <div class="card">
                        <h6 class="card-header">
                            @lang('global.form') ({{ Str::upper($data['lang']['name']) }})
                        </h6>
                        <hr class="border-light m-0">
                        <div class="card-body">
                            @foreach ($data['files'] as $key => $value)
                            <div class="form-group row">
                                <div class="col-md-2 text-md-right">
                                    <label class="col-form-label text-sm-right">{{ Str::replace('_', ' ', Str::upper($key)) }}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control text-bolder" name="lang[{{ $key }}]" placeholder="enter text...">{{ $value }}</textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-footer justify-content-center">
                            <div class="box-btn">
                                <button class="btn btn-main w-icon" type="submit" title="@lang('global.save_change')">
                                    <i class="fi fi-rr-disk"></i>
                                    <span>@lang('global.save_change')</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection