<!-- jQuery.min.js -->
<script src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>

<!-- jQuery Global-->
<script src="{{ asset('assets/frontend/js/fill.box.js') }}"></script>
<script src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/frontend/js/main.js') }}"></script>
{{-- @vite(['public/assets/frontend/js/jquery.min.js', 'public/assets/frontend/js/fill.box.js', 'public/assets/frontend/js/bootstrap.min.js', 'public/assets/frontend/js/main.js']) --}}

<!-- jQuery addtional-->
@yield('scripts')
