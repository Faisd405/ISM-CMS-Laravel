<div class="preloader d-flex align-items-center justify-content-center overflow-hidden">
    <div class="preloader-logo d-flex flex-column align-items-center justify-content-center">
        @foreach (explode(' ', config('cmsConfig.general.website_name')) as $websiteString)
            <div class="overflow-hidden">
                <div class="title text-uppercase line-height-sm text-dark">{{ $websiteString }}</div>
            </div>
        @endforeach
    </div>
    <div class="logo"></div>
</div>
