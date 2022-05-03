<nav class="layout-footer footer bg-footer-theme">
    <div class="container-fluid d-flex flex-wrap justify-content-center justify-content-sm-between align-items-center container-p-x pb-3">
      <div class="pt-3">
        © {{ now()->format('Y') }} <span class="footer-text font-weight-bolder"> {!! $config['website_name'] !!}</span> All Rights Reserved.
      </div>
      <div class="pt-3">
        @if (config('cms.setting.copyright.show') == true)
        <span class="footer-text d-flex">Developed By : 
          <a href="{{ config('cms.setting.copyright.url') }}" 
            target="_blank" 
            title="{{ config('cms.setting.copyright.title') }}" 
            style="display: inline-block; width: 100px; margin-left: 10px;">
            <img src="{{ asset('assets/backend/images/logo-4vm.svg') }}"></a>
          </span>
        @endif
      </div>
      <!-- <div>
        {{-- <a href="javascript:void(0)" class="footer-link pt-3">About Us</a>
        <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Help</a>
        <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Contact</a>
        <a href="javascript:void(0)" class="footer-link pt-3 ml-4">Terms &amp; Conditions</a> --}}
      </div> -->
    </div>
</nav>
