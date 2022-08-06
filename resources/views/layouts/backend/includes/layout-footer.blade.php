<nav class="layout-footer footer bg-footer-theme">
    <div
        class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3">
        <div class="pt-3">
            <span class="footer-text font-weight-bolder">{!! config('cmsConfig.website_name') !!}</span> © {{ now()->format('Y') }}
        </div>
        <div class="pt-3">
            @if (config('cms.setting.copyright.show') == true)
            <span class="footer-text d-flex">Developed By : 
                <a href="{{ config('cms.setting.copyright.url') }}" 
                    target="_blank" 
                    title="{{ config('cms.setting.copyright.title') }}" 
                    style="display: inline-block; width: 100px; margin-left: 10px;">
                    <img src="{{ asset('assets/backend/img/4vm.svg') }}">
                </a>
            </span>
            @endif
        </div>
    </div>
</nav>