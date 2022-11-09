<section class="content-wrap pb-0" id="home-about" data-nav-color="dark">
    <div class="container">
        <div class="row g-0 align-items-center justify-content-between">
            <div class="col-lg-6">
                <div class="section-img">
                    <div class="section-img-1 ratio ratio-1x1 anim-scroll-img" data-aos>
                        <img src="{{ asset('assets/frontend/img/integra_about_featured.jpg') }}" alt="" class="thumb">
                    </div>
                    <div class="section-img-2 ratio ratio-1x1 anim-scroll-img delay-200" data-aos>
                        <img src="{{ asset('assets/frontend/img/integra_about_approach1.jpg') }}" alt="" class="thumb">
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-column">
                    <div class="main-title mb-5">
                        <div class="subtitle mb-4 mb-xl-5 split-text text-danger" data-aos>@lang($widget['module']['page']->fieldLang('title'))</div>
                        <h1 class="title fw-700 title-display-1 text-uppercase line-height-sm split-text" data-aos>{!! $widget->fieldLang('title') !!}</h1>
                    </div>
                    <div class="caption-text anim-scroll-up mb-6" data-aos>
                        {!! $widget->fieldLang('description') !!}
                    </div>
                    <ul class="list-unstyled list-bordered list-icon mb-0 anim-scroll-up mt-auto" data-aos>
                        @foreach ($widget['module']['childs'] as $child)
                            <li class="d-flex align-items-center">
                                <i class="icon fa-light {{ isset($child['custom_fields']['icon']) ? $child['custom_fields']['icon'] : 'fa-desktop' }} text-danger"></i>
                                <a href="{{route('page.read.child.' . $child->slug)}}" class="d-inline-flex fs-16 text-dark">
                                    <div class="label-btn span-2-red">{!! $child->fieldLang('title') !!}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
