@extends('layouts.frontend.layout')

@if (config('cms.setting.recaptcha') == true)
    @section('jshead')
        {!! htmlScriptTagJsApi() !!}
    @endsection
@endif

@section('body-attr')
    class="single-page"
@endsection

@section('content')
    <section class="page-header">
        <div class="page-header-bg thumb overflow-hidden">
            <div class="thumb overflow-hidden">
                <img src="{{ $data['read']['bannerSrc'] }}" alt="" class="thumb" data-rellax data-rellax-speed="-4">
                <div class="bg-overlay"></div>
            </div>
        </div>
        <div class="page-header-content d-flex flex-column">
            <div class="container mt-auto">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <div class="main-title">
                            <div class="subtitle mb-4 text-danger split-text">{!! $data['read']->fieldLang('name') !!}</div>
                            <h1 class="title fw-700 split-text line-height-sm">{!! $data['read']->fieldLang('body') !!}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="ornament ornament-1">
            <img src="assets/img/ornament01.svg" class="w-100 anim-load-left delay-400" alt="">
        </div>
    </section>

    <section class="content-wrap page-content" data-nav-color="dark">
        <div class="container">
            <div class="row g-0 justify-content-between">
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="contact-information anim-scroll-up" data-aos>
                        <div class="main-title mb-4">
                            <h3 class="title fw-700">@lang('text.inquiry_information')</h3>
                        </div>
                        <div class="contact-list">
                            <h5 class="title mb-4">@lang('text.office')</h5>
                            <ul class="list-unstyled mb-0">
                                <li>
                                    <i class="fa-light fa-house text-danger"></i>
                                    <span>{{ config('cmsConfig.general.address') }}</span>
                                </li>
                                <li>
                                    <i class="fa-light fa-envelope text-danger"></i>
                                    <a href="#!" class="link">{{ config('cmsConfig.general.email') }}</a>
                                </li>
                                <li>
                                    <i class="fa-light fa-phone text-danger"></i>
                                    <a href="#!" class="link">{{ config('cmsConfig.general.phone') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="contact-list">
                            <h5 class="title mb-4">@lang('text.delivery_center')</h5>
                            <ul class="list-unstyled mb-0">
                                <li>
                                    <i class="fa-light fa-house text-danger"></i>
                                    <span>{{ config('cmsConfig.general.address_2') }}</span>
                                </li>
                                <li>
                                    <i class="fa-light fa-envelope text-danger"></i>
                                    <a href="#!" class="link">{{ config('cmsConfig.general.email_2') }}</a>
                                </li>
                                <li>
                                    <i class="fa-light fa-phone text-danger"></i>
                                    <a href="#!" class="link">{{ config('cmsConfig.general.phone_2') }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-form anim-scroll-up" data-aos>
                        <div class="main-title mb-4">
                            <h3 class="title fw-700">@lang('module/inquiry.caption')</h3>
                        </div>
                        @if (session('success') || Cookie::get($data['read']['slug']))
                            <div class="text-center">
                                <h2>@lang('text.message_success')</h2>
                                <article>
                                    {!! $data['read']->fieldLang('after_body') !!}
                                </article>
                            </div>
                        @else
                            <form action="{{ route('inquiry.submit', ['id' => $data['read']->id]) }}" method="post">
                                @csrf
                                <div class="row justify-content-between mb-4 mb-xl-5">
                                    @foreach ($data['fields'] as $field)
                                        <div class="{{ $field['type'] == 0 ? 'col-lg-6' : 'col-lg-12' }}">
                                            <div class="form-group">
                                                <div class="input-custom">
                                                    <label class="form-label">{{ $field->fieldLang('placeholder') }}
                                                        @foreach ($field['validation'] as $validation)
                                                            @if ($validation == 'required')
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        @endforeach
                                                    </label>
                                                    @if ($field->type == '1')
                                                        <textarea name="{{ $field->name }}" type="text" rows="2" class="form-control"></textarea>
                                                        <div class="input-line"></div>
                                                    @else
                                                        <input type="text" class="form-control"
                                                            name="{{ $field->name }}"
                                                            @if (isset($field->validation)) @foreach ($field->validation as $validation)
                                                                {{ $validation }}
                                                            @endforeach @endif>
                                                    @endif
                                                    <div class="input-line"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row g-0 justify-content-end">
                                    <div class="col-lg-6 d-grid text-center">
                                        <button type="submit" class="btn btn-danger">
                                            <div class="label-btn span-center subtitle">@lang('text.send_message')</div>
                                            <i class="fa-light fa-arrow-right-long ms-3"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('styles')
    <style>
        .grecaptcha-badge {
            width: 70px !important;
            overflow: hidden !important;
            transition: all 0.3s ease !important;
            left: 4px !important;
        }

        .grecaptcha-badge:hover {
            width: 256px !important;
        }
    </style>
@endsection
