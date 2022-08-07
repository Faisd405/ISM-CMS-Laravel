@extends('layouts.frontend.layout')

@if (config('cms.setting.recaptcha') == true)    
@section('jshead')
{!! htmlScriptTagJsApi() !!}
@endsection
@endif


@section('content')
<section class="section-page-header" id="section-page-header" data-scroll-section>
    <div class="page-header-bg">
        <div class="thumb overflow-hidden">
            <img class="thumb" src="{{ $data['banner'] }}" data-scroll data-scroll-speed="-2"
                title="{!! $data['read']['banner']['title'] ?? $data['read']->fieldLang('name') !!}" 
            alt="{!! $data['read']['banner']['alt'] ?? $data['banner'] !!}">
        </div>
    </div>
    <div class="page-header-content flex-grow-1">
        <div class="container">
            <div class="row g-0 justify-content-center">
                <div class="col-lg-10">
                    <div class="main-title text-center mb-0">
                        <div class="subtitle animated-load-up">{!! $data['read']->fieldLang('name') !!}</div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content-wrap section-page-content" data-scroll-section>
    <div class="container animated-load-down delay-500">
        <div class="contact-form">
            @if ($data['read']['config']['show_form'] == true)
                @if (session('success'))
                <div class="text-center">
                    {{-- {!! $data['read']->fieldLang('after_body') !!} --}}
                    test
                </div>
                @else
                    {{-- @if (Cookie::get($data['read']['slug']))
                    <div class="text-center">
                        {!! $data['read']->fieldLang('after_body') !!}
                    </div>
                    @else --}}
                    <form class="row g-0 justify-content-center" action="{{ route('event.submit', ['id' => $data['read']['id']]) }}" method="POST">
                        @csrf
                        <div class="col-lg-8">
                            <div class="row">
                                @foreach ($data['fields'] as $field)
                                <div class="{!! $field['properties']['class'] !!}">
                                    <div class="mb-30">
                                        <div class="input-custom">
                                            <label class="form-label fw-500 fs-16">{!! $field->fieldLang('label') !!} {!! !empty($field['validation']) ? '<span class="text-warning">*</span>' : '' !!}</label>
                                            @switch($field['type'])
                                                @case(1)
                                                <textarea class="form-control fs-16" name="{{ $field['name'] }}" placeholder="{!! $field->fieldLang('placeholder') !!}">{{ old($field['name']) }}</textarea>
                                                    @break
                                                @default
                                                <input type="{!! $field['properties']['type'] !!}" class="form-control fs-16"
                                                    name="{{ $field['name'] }}" value="{{ old($field['name']) }}" placeholder="{!! $field->fieldLang('placeholder') !!}">
                                            @endswitch
                                            <div class="input-line"></div>
                                        </div>
                                        @if ($errors->has($field['name']))
                                        <div class="text-small" style="color: red;"><i>*{!! $errors->first($field['name']) !!}</i></div>
                                        @enderror
                                    </div>
                                </div>
                                @endforeach
                                @if (config('recaptcha.version') == 'v2')
                                <div class="col-lg-12">
                                    <div class="mb-30">
                                        {!! htmlFormSnippet() !!}
                                        @error('g-recaptcha-response')
                                        <div class="text-small" style="color: red;"><i>*{{ $message }}</i></div>
                                        @enderror
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-4 d-grid">
                                    <button type="submit" class="btn btn-outline-warning rounded-pill text-dark" title="@lang('text.send_message_caption')"><div class="label-btn span-2-white">@lang('text.send_message_caption')</div></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    {{-- @endif --}}
                @endif
            @endif
        </div>
    </div>
</section>
@endsection