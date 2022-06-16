@extends('layouts.backend.application')

@section('body-attr')
class="alsen"
@endsection

@section('layout-content')
<!-- Layout wrapper -->
<div class="layout-wrapper {{ config('cms.setting.layout') == 1 ? 'layout-1 layout-without-sidenav' : 'layout-2' }}">
    <div class="layout-inner">

        @if (config('cms.setting.layout') == 1)
            <!-- Layout navbar -->
            @include('layouts.backend.includes.layout-navbar')
        @else
            <!-- Layout sidenav -->
            @include('layouts.backend.includes.layout-sidenav')
        @endif

        <!-- Layout container -->
        <div class="layout-container">
            @if (config('cms.setting.layout') == 0)
                <!-- Layout navbar -->
                @include('layouts.backend.includes.layout-navbar')
                @include('components.breadcrumbs-backend')
            @endif
            
            <!-- Layout content -->
            <div class="layout-content">
                @if (config('cms.setting.layout') == 1)
                    <!-- Layout navbar -->
                    @include('layouts.backend.includes.layout-sidenav')
                    @include('components.breadcrumbs-backend')
                @endif

                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    @yield('content')
                </div>
                <!-- / Content -->

                <!-- Layout footer -->
                @include('layouts.backend.includes.layout-footer')
            </div>
            <!-- Layout content -->

        </div>
        <!-- / Layout container -->

    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-sidenav-toggle"></div>
</div>
<!-- / Layout wrapper -->
@endsection
