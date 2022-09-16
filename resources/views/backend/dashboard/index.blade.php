@extends('layouts.backend.layout')

@section('content')
<h4 class="media align-items-center font-weight-bold py-3 mb-4">
    <img src="{{ Auth::user()['avatar'] }}" alt="{{ Auth::user()['name'] }} photo" class="ui-w-50 rounded-circle box-avatar">
    <div class="media-body ml-3">
        @lang('module/dashboard.welcome_caption'), {{ Auth::user()['name'] }} !
        <div class="text-muted text-tiny mt-1">
            <small class="font-weight-normal">
                @lang('module/dashboard.today_caption') {{ now()->isoFormat('dddd, D MMMM Y') }} (<em id="timenow"></em>)
            </small>
        </div>
    </div>
</h4>

@if (config('cmsConfig.dev.maintenance') == 1)
<div class="alert alert-warning alert-dismissible fade show">
  <i class="fi fi-rr-sensor-alert"></i>
  @lang('module/dashboard.maintenance_caption')
</div>
@endif

@if (Auth::user()->can('pages') && config('cms.module.page.active') == true 
    || Auth::user()->can('content_posts') && config('cms.module.content.post.active') == true)
<div class="row">
    <div class="d-flex col-xl-12 align-items-stretch">

        <!-- Stats + Links -->
        <div class="card d-flex w-100 mb-4">
            <div class="row no-gutters row-bordered h-100">
                @if (Auth::user()->can('pages') && config('cms.module.page.active') == true)
                <div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">

                    <a href="{{ route('page.index') }}"
                        class="card-body media align-items-center text-body">
                        <i class="fi fi-rr-menu-burger display-4 d-block text-main"></i>
                        <span class="media-body d-block ml-3">
                            <span class="text-big font-weight-bolder">{{ $data['counter']['page'] }}</span><br>
                            <small class="text-muted">@lang('module/page.title')</small>
                        </span>
                    </a>

                </div>
                @endif
                @if (Auth::user()->can('content_posts') && config('cms.module.content.post.active') == true)
                <div class="d-flex col-sm-6 col-md-4 col-lg-6 align-items-center">

                    <a href="{{ route('content.section.index') }}"
                        class="card-body media align-items-center text-body">
                        <i class="fi fi-rr-edit display-4 d-block text-main"></i>
                        <span class="media-body d-block ml-3">
                            <span class="text-big"><span class="font-weight-bolder">{{ $data['counter']['post'] }}</span><br>
                            <small class="text-muted">@lang('module/content.post.title')</small>
                        </span>
                    </a>

                </div>
                @endif
            </div>
        </div>
        <!-- / Stats + Links -->

    </div>
</div>
@endif

@can('visitor')
    @if (!empty(env('ANALYTICS_VIEW_ID')))
    <div class="card">
        <div class="card-header">
            <h5 class="my-2">
                <i class="fi fi-rr-calendar-clock text-main"></i> @lang('module/dashboard.latest_visitor')
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr id="analytics-head">

                    </tr>
                </thead>
                <tbody>
                    <tr id="analytics-body">

                    </tr>
                </tbody>
            </table>
        </div>
        <a href="{{ route('visitor') }}" class="card-footer d-block text-center text-body small font-weight-semibold">
            @lang('global.show_more')
        </a>
    </div>
    @endif
@endcan

<div class="row">
    @if (Auth::user()->can('content_posts') && config('cms.module.content.post.active') == true)
    <div class="col-md-6">
        <!-- Latest Post -->
        <div class="card mb-4">
            <h6 class="card-header">
                @lang('module/dashboard.latest_post')
            </h6>
            <div class="table-responsive">
                <table class="table card-table">
                    <thead>
                        <tr>
                            <th colspan="2">@lang('module/content.post.label.title')</th>
                            <th style="width: 100px;">@lang('global.hits')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['list']['posts'] as $post)
                        <tr>
                            <td class="align-middle" style="width: 75px">
                                <img class="ui-w-40" src="{{ $post['cover_src'] }}" alt="">
                            </td>
                            <td class="align-middle">
                                <span class="text-body">{!! Str::limit($post->fieldLang('title'), 60) !!}</span> &nbsp;
                                <a href="{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}" title="@lang('global.view_detail')" target="_blank">
                                    <i class="fi fi-rr-link text-bold" style="font-size: 14px;"></i>
                                </a>
                            </td>
                            <td class="align-middle"><span class="badge badge-info">{{ $post['hits'] }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" align="center">
                                <i>
                                <strong class="text-muted">
                                    ! @lang('global.data_attr_empty', [
                                            'attribute' => __('module/content.post.caption')
                                        ]) !
                                </strong>
                                </i>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('content.section.index') }}"
                class="card-footer d-block text-center text-body small font-weight-semibold">
                @lang('global.show_more')
            </a>
        </div>
        <!-- / Latest Post -->
    </div>
    @endif
    @if (Auth::user()->can('inquiries') && config('cms.module.inquiry.active') == true)
    <div class="col-md-6">

        <!-- Feed -->
        <div class="card mb-4">
            <h6 class="card-header">@lang('module/dashboard.latest_submit_inquiry')</h6>
            <div class="card-body">
                @forelse ($data['list']['inquiries'] as $inquiry)
                @php
                    $fields = $inquiry->inquiry->fields()->firstWhere(['publish' => 1, 'approved' => 1]);
                @endphp
                <div class="media pb-1 mb-3">
                    <img src="{{ asset(config('cms.files.avatar.file')) }}" class="d-block ui-w-40 rounded-circle" alt="">
                    <div class="media-body flex-truncate ml-3">
                        <a href="javascript:void(0)">{!! $inquiry['fields'][$fields['name']] !!}</a>
                        <span class="text-muted">From</span>
                        <a href="{{ route('inquiry.form', ['inquiryId' => $inquiry['inquiry_id'], 'q' => $inquiry['fields'][$fields['name']]]) }}">{{ $inquiry['inquiry']->fieldLang('name') }}</a>
                        <p class="text-truncate my-1"></p>
                        <div class="clearfix">
                            <span class="float-left text-muted small">{{ $inquiry['submit_time']->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="media pb-1 mb-3">
                    <div class="media-body flex-truncate ml-3 text-center">
                        <i>
                        <strong class="text-muted">
                            ! @lang('global.data_attr_empty', [
                                'attribute' => __('module/inquiry.caption')
                            ]) !
                        </strong>
                        </i>
                    </div>
                </div>
                @endforelse
            </div>
            <a href="{{ route('inquiry.index') }}"
                class="card-footer d-block text-center text-body small font-weight-semibold">
                @lang('global.show_more')
            </a>
        </div>
        <!-- / Feed -->

    </div>
    @endif
</div>
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

        $.ajax({
            url : "/admin/dashboard/analytics",
            type : "GET",
            dataType : "json",
            data : {},
            success:function(data) {
                $('#analytics-head').html(' ');
                $('#analytics-body').html(' ');

                $.each(data.data ,function(index, value) {
                    $('#analytics-head').append(`
                        <th class="text-center">`+value.date+`</th>
                    `);
                    $('#analytics-body').append(`
                        <td class="text-center">
                            <span class="badge badge-main">`+value.visitor+`</span>
                        </td>
                    `);
                });
            }
        });
    });
</script>
@endsection
