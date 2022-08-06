@extends('layouts.backend.application')

@section('layout-content')
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-{{ config('cms.setting.layout') == 3 ? '1 layout-without-sidenav' : config('cms.setting.layout') }}">
        <div class="layout-inner">
            
            @if (config('cms.setting.layout') == 2)
            <!-- Layout sidenav -->
            @include('layouts.backend.includes.layout-sidenav')
            <!-- / Layout sidenav -->
            @else
            <!-- Layout navbar -->
            @include('layouts.backend.includes.layout-navbar')
            <!-- / Layout navbar -->
            @endif

            <!-- Layout container -->
            <div class="layout-container">
                @if (config('cms.setting.layout') == 1)
                <!-- Layout navbar -->
                @include('layouts.backend.includes.layout-sidenav')
                <!-- / Layout navbar -->
                @endif

                @if (config('cms.setting.layout') == 2)
                <!-- Layout navbar -->
                @include('layouts.backend.includes.layout-navbar')
                <!-- / Layout navbar -->
                @endif

                <!-- Layout content -->
                <div class="layout-content">

                    @if (config('cms.setting.layout') == 3)
                    <!-- Layout sidenav -->
                    @include('layouts.backend.includes.layout-sidenav')
                    <!-- / Layout sidenav -->
                    @endif

                    <!-- Content -->
                    <div class="container-fluid flex-grow-1 container-p-y pb-0">
                        @include('components.breadcrumbs-backend')
                        
                        @yield('content')
                    </div>

                    <!-- Layout footer -->
                    @include('layouts.backend.includes.layout-footer')
                    <!-- / Layout footer -->
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