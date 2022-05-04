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

    {{-- latest post --}}
    <div class="col-md-6">
        <div class="card mb-4 card-list">
            <h6 class="card-header"><i class="las la-newspaper" style="font-size: 1.3em;"></i> @lang('module/content.post.title')</h6>
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
                            <a href="{{ route('content.post.read.'.$post['section']['slug'], ['slugPost' => $post['slug']]) }}" class="btn icon-btn btn-sm btn-primary" title="@lang('global.view_detail')">
                                <i class="las la-external-link-alt"></i>
                            </a>
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

    {{-- latest inquiry --}}
    <div class="col-md-6">
        <div class="card mb-4 card-list">
          <h6 class="card-header">
            <div class="title-text"><i class="las la-envelope" style="font-size: 1.3em;"></i> @lang('module/inquiry.title')</div>
          </h6>
          <div class="card-body">
  
            @forelse ($data['list']['inquiries'] as $inquiry)
            <div class="media pb-1 mb-3">
              <img src="{{ asset(config('cms.files.avatars.file')) }}" class="d-block ui-w-40 rounded-circle" alt="">
              <div class="media-body flex-truncate ml-3">
                <a href="javascript:void(0)">{!! $inquiry['fields']['name'] !!}</a>
                <span class="text-muted">Dari</span>
                <a href="{{ route('inquiry.form', ['inquiryId' => $inquiry['inquiry_id']]) }}">{{ $inquiry['inquiry']->fieldLang('name') }}</a>
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
    });
</script>
@endsection
