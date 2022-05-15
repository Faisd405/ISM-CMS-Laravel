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

   
<div class="row"> 
    {{-- counter --}}
    @if (Auth::user()->can('pages') && config('cms.module.page.active') == true)
    <div class="col-sm-6 col-xl-6">
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="las la-bars display-4 text-primary"></div>
            <div class="ml-3">
              <a href="{{ route('page.index') }}" class="text-muted small">@lang('module/page.title')</a>
              <div class="text-large">{{ $data['counter']['page'] }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    @if (Auth::user()->can('content_posts') && config('cms.module.content.post.active') == true)
    <div class="col-sm-6 col-xl-6">
        <div class="card mb-4">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="las la-newspaper display-4 text-primary"></div>
              <div class="ml-3">
                <a href="{{ route('content.section.index') }}" class="text-muted small">@lang('module/content.post.title')</a>
                <div class="text-large">{{ $data['counter']['post'] }}</div>
              </div>
            </div>
          </div>
        </div>
    </div>
    @endif

    @can('visitor')
      @if (!empty(env('ANALYTICS_VIEW_ID')))
      {{-- visitor --}}
      <div class="col-md-12">
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="card-header-title mt-0 mb-0"><i class="las la-users" style="font-size: 1.3em;"></i> @lang('module/dashboard.latest_visitor')</h6>
          </div>
          <div class="table-responsive">
            <table class="table card-table">
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
          <div class="card-footer">
            <a href="{{ route('visitor') }}" class="d-block text-center text-body small font-weight-semibold">@lang('global.show_more')</a>
          </div>
        </div>
      </div>
      @endif
    @endcan

    @if (Auth::user()->can('content_posts') && config('cms.module.content.post.active') == true)
    {{-- latest post --}}
    <div class="col-md-6">
        <div class="card mb-4 card-list">
            <h6 class="card-header"><i class="las la-newspaper" style="font-size: 1.3em;"></i> @lang('module/dashboard.latest_post')</h6>
            <div class="table-responsive">
                <table class="table card-table">
                <thead>
                    <tr>
                        <th colspan="2">@lang('module/content.post.label.field1')</th>
                        <th>@lang('global.hits')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['list']['posts'] as $post)
                    <tr>
                        <td class="align-middle" style="width: 75px">
                            <img class="ui-w-40" src="{{ $post->coverSrc() }}" alt="">
                        </td>
                        <td class="align-middle">
                            <a href="javascript:void(0)" class="text-body">{!! Str::limit($post->fieldLang('title'), 40) !!}</a>
                        </td>
                        <td class="align-middle"><span class="badge badge-info">{{ $post['hits'] }}</span></td>
                        <td class="align-middle">
                          @if ($post['config']['is_detail'] == true)
                            <a href="{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.view_detail')">
                                <i class="las la-external-link-alt"></i>
                            </a>
                          @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" align="center">
                            <i>
                            <strong style="color:red;">
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
                <a href="{{ route('content.section.index') }}" class="card-footer d-block text-center text-body small font-weight-semibold">@lang('global.show_more')</a>
            </div>
        </div>
    </div>
    @endif

    @if (Auth::user()->can('inquiries') && config('cms.module.inquiry.active') == true)
    {{-- latest inquiry --}}
    <div class="col-md-6">
        <div class="card mb-4 card-list">
          <h6 class="card-header">
            <div class="title-text"><i class="las la-envelope" style="font-size: 1.3em;"></i> @lang('module/dashboard.latest_submit_inquiry')</div>
          </h6>
          <div class="card-body">
  
            @forelse ($data['list']['inquiries'] as $inquiry)
            <div class="media pb-1 mb-3">
              <img src="{{ asset(config('cms.files.avatars.file')) }}" class="d-block ui-w-40 rounded-circle" alt="">
              <div class="media-body flex-truncate ml-3">
                <a href="javascript:void(0)">{!! $inquiry['fields']['name'] !!}</a>
                <span class="text-muted">From</span>
                <a href="{{ route('inquiry.form', ['inquiryId' => $inquiry['inquiry_id'], 'q' => $inquiry['fields']['email']]) }}">{{ $inquiry['inquiry']->fieldLang('name') }}</a>
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
                  <strong style="color:red;">
                    ! @lang('global.data_attr_empty', [
                        'attribute' => __('module/inquiry.caption')
                    ]) !
                  </strong>
                </i>
              </div>
            </div>
            @endforelse
  
          </div>
          <a href="{{ route('inquiry.index') }}" class="card-footer d-block text-center text-body small font-weight-semibold">@lang('global.show_more')</a>
        </div>
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
                  <span class="badge badge-info">`+value.visitor+`</span>
                </td>
                `);
              });
            }
        });
    });
</script>
@endsection
