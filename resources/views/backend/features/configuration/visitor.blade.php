@extends('layouts.backend.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-10 col-md-10">

        @if (isset($data['error']) && empty(env('ANALYTICS_VIEW_ID')))
        <div class="alert alert-warning alert-dismissible">
            @lang('feature/configuration.visitor.warning_caption')
        </div>
        @else
        {{-- Filter --}}
        <div class="card">
            <div class="card-body d-flex flex-wrap justify-content-between">
                <div class="d-flex w-100 w-xl-auto">
                    <button type="button" class="btn btn-dark icon-btn-only-sm btn-sm mr-2" title="@lang('global.filter')" id="filter-btn">
                        <i class="las la-filter"></i> <span>@lang('global.filter')</span>
                    </button>
                    @if ($totalQueryParam > 0)
                    <a href="{{ url()->current() }}" class="btn btn-warning icon-btn-only-sm btn-sm" title="Clear @lang('global.filter')">
                        <i class="las la-redo-alt"></i> <span>Clear @lang('global.filter')</span>
                    </a>
                    @endif
                </div>
            </div>
            <hr class="m-0">
            <div class="card-body" id="{{ $totalQueryParam == 0 ? 'filter-form' : '' }}">
                <form action="" method="GET">
                    <div class="form-row align-items-center">
                        <div class="col-md">
                            <div class="form-group">
                                <label class="form-label">@lang('global.filter')</label>
                                <select id="filter" class="custom-select" name="filter">
                                    <option value=" " selected disabled>@lang('global.select')</option>
                                    @foreach (__('feature/configuration.visitor.filter') as $key => $val)
                                    <option value="{{ $key }}" {{ Request::get('filter') == ''.$key.'' ? 'selected' : '' }} 
                                        title="@lang('global.limit') {{ $val }}">{{ $val }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <h6 class="card-header">@lang('feature/configuration.visitor.label.card1')</h6>
                    <div class="d-flex justify-content-center">
                        <canvas id="chart-pie" height="450" class="chartjs-demo"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <h6 class="card-header">@lang('feature/configuration.visitor.label.card2')</h6>
                    <canvas id="chart-bars" height="450" class="chartjs-demo"></canvas>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4">
                    <h6 class="card-header">@lang('feature/configuration.visitor.title')</h6>
                    <canvas id="chart-graph" height="450" class="chartjs-demo"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <h6 class="card-header">@lang('feature/configuration.visitor.label.card3')</h6>
                    <div class="table-responsive">
                    <table class="table card-table">
                        <thead>
                            <tr>
                                <th>@lang('feature/configuration.visitor.label.field1')</th>
                                <th class="text-center">@lang('global.hits')</th>
                                <th class="text-center">@lang('feature/configuration.visitor.title')</th>
                                <th style="width: 160px;">@lang('feature/configuration.visitor.label.field2')</th>
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
                                <span class="badge badge-primary">{{ $v['visitors'] }}</span>
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
                    <h6 class="card-header">@lang('feature/configuration.visitor.label.card3')</h6>
                    <div class="table-responsive">
                    <table class="table card-table">
                        <thead>
                            <tr>
                                <th>@lang('feature/configuration.visitor.label.field1')</th>
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
        responsive: false,
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
        responsive: false,
        maintainAspectRatio: false
        }
    });
</script>
@endif
@endsection