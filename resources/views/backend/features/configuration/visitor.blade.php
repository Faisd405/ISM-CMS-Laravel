@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-12 col-lg-12 col-md-12">

        @if (isset($data['error']))
        <div class="alert alert-warning alert-dismissible">
            {{ $data['error'] }}
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h5 class="my-2">
                    @lang('global.visitor')
                </h5>
                <div class="box-btn">
                    <button type="button" class="btn btn-default w-icon" data-toggle="modal"
                        data-target="#modals-slide" title="@lang('global.filter')">
                        <i class="fi fi-rr-filter"></i>
                        <span>@lang('global.filter')</span>
                    </button>
                </div>
                <!-- Modal Filter -->
                <div class="modal modal-slide fade" id="modals-slide">
                    <div class="modal-dialog">
                        <form class="modal-content pb-0" action="" method="GET">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
                            <div class="modal-body mt-3">
                                <div class="form-group">
                                    <label class="form-label" for="filter">Range</label>
                                    <select id="filter" class="custom-select" name="filter">
                                        <option value=" " selected disabled>@lang('global.select')</option>
                                        @foreach (__('feature/configuration.visitor.filter') as $key => $val)
                                        <option value="{{ $key }}" {{ Request::get('filter') == ''.$key.'' ? 'selected' : '' }} 
                                            title="Range {{ $val }}">{{ $val }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="box-btn justify-content-between w-100 m-0">
                                    @if ($totalQueryParam > 0)
                                    <a href="{{ url()->current() }}" class="btn btn-default w-100 text-bolder">Clear @lang('global.filter')</a>
                                    @endif
                                    <button type="submit" class="btn btn-main w-100">@lang('global.filter')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="my-2">
                            @lang('feature/configuration.visitor.label.session')
                        </h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <canvas id="chart-pie" height="450" class="chartjs-demo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="my-2">
                            @lang('feature/configuration.visitor.label.most_browser')
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chart-bars" height="450" class="chartjs-demo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="my-2">
                            @lang('feature/configuration.visitor.title')
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chart-graph" height="450" class="chartjs-demo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="my-2">
                            @lang('feature/configuration.visitor.label.visitor_page')
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th>@lang('feature/configuration.visitor.label.title')</th>
                                    <th class="text-center">@lang('global.hits')</th>
                                    <th class="text-center">@lang('feature/configuration.visitor.title')</th>
                                    <th style="width: 160px;">@lang('feature/configuration.visitor.label.date')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['vp']->take(5)->sortbydesc('visitors') as $v)
                                <tr>
                                    <td title="{{ $v['pageTitle'] }}">{{ Str::limit($v['pageTitle'], 40) }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $v['pageViews'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-main">{{ $v['visitors'] }}</span>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($v['date'])->format('d F Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="my-2">
                            @lang('feature/configuration.visitor.label.top_page')
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table card-table">
                            <thead>
                                <tr>
                                    <th>@lang('feature/configuration.visitor.label.title')</th>
                                    <th class="text-center">@lang('global.hits')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['top']->take(5) as $tt)
                                <tr>
                                    <td>
                                        <a href="{{ url('/').$tt['url'] }}" title="{{ $tt['pageTitle'] }}">
                                            @if ($tt['pageTitle'] == '(not set)')
                                                @lang('menu.frontend.home') 
                                            @else
                                                {{ Str::limit($tt['pageTitle'], 65) }}
                                            @endif
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $tt['pageViews'] }}</span> 
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/backend/vendor/libs/chartjs/chartjs.js') }}"></script>
@endsection

@section('jsbody')
<script>
    $('#filter').on('change', function () {
        var url = $(this).val();
        if (url) {
            window.location = '?filter='+url;
        }

        return false;
    });
</script>
@if (!isset($data['error']) && !empty(env('ANALYTICS_VIEW_ID')))
<script>
    //pie
    var pieChart = new Chart(document.getElementById('chart-pie').getContext("2d"), {
        type: 'pie',
        data: {
        labels: {!! json_encode($data['session_visitor']['label']) !!},
            datasets: [{
                data: {!! json_encode($data['session_visitor']['total']) !!},
                backgroundColor: ['#0084ff', '#ec1c24'],
                hoverBackgroundColor: ['#0084ff', '#ec1c24']
            }]
        },

        options: {
        responsive: false,
        maintainAspectRatio: false
        }
    });

    //barchart
    var barsChart = new Chart(document.getElementById('chart-bars').getContext("2d"), {
        type: 'bar',
        data: {
            labels: {!! json_encode($data['browser_visitor']['label']) !!},
            datasets: [{
                label: '@lang('feature/configuration.visitor.title')',
                data: {!! json_encode($data['browser_visitor']['total']) !!},
                borderWidth: 1,
                backgroundColor: '#ec1c24',
                borderColor: '#ec1c24',
            }]
        },

        // Demo
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    //graphchart
    var graphChart = new Chart(document.getElementById('chart-graph').getContext("2d"), {
        type: 'line',
        data: {
        labels: {!! json_encode($data['total_visitor']['label']) !!},
        datasets: [{
            label: '@lang('feature/configuration.visitor.title')',
            data: {!! json_encode($data['total_visitor']['total']) !!},
            borderWidth: 1,
            backgroundColor: 'rgb(0, 132, 255, 1)',
            borderColor: '#0084ff',
        }],
        },

        // Demo
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endif
@endsection