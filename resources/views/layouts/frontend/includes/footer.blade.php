<footer class="section-footer" id="get-in-touch" data-nav-color="light">
    <section class="content-wrap top-footer pb-0">
        <div class="container">
            <div class="row gx-0 gx-lg-5" id="footer-collapse">
                @foreach ($menu['footer'] as $footer)
                    <div class="col-lg-4 collapse-menu footer-wrap anim-scroll-up" data-aos>
                        <h5 class="title text-white" data-bs-target="#footer-collapse-{{ $footer['id'] }}">
                            {{ $footer['module_data']['title'] }}</h5>
                        <div class="collapse show" id="footer-collapse-{{$footer['id']}}" data-bs-parent="#footer-collapse">
                            <ul class="list-unstyled nav-footer mb-0">
                                @foreach ($footer->childs as $child)
                                    <li>
                                        <a href="{{ $child['module_data']['routes'] }}" class="text-muted">
                                            <div class="label-btn d-inline-flex span-2-white">
                                                {{ $child['module_data']['title'] }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row gx-5">
                <div class="col-lg-4 footer-wrap d-flex align-items-center">
                    <div class="logo-footer anim-scroll-up" data-aos></div>
                </div>
                <div class="col-lg-4 footer-wrap">
                    <div class="contact-footer anim-scroll-up" data-aos>
                        <h5 class="title mb-4 text-white">@lang('text.office')</h5>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class="fa-light fa-house text-danger"></i>
                                <span class="text-white">
                                    {{ config('cmsConfig.general.address') }}
                                </span>
                            </li>
                            <li>
                                <i class="fa-light fa-envelope text-danger"></i>
                                <a href="#!" class="link text-white">
                                    {{ config('cmsConfig.general.email') }}
                                </a>
                            </li>
                            <li>
                                <i class="fa-light fa-phone text-danger"></i>
                                <a href="#!" class="link text-white">
                                    {{ config('cmsConfig.general.phone') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 footer-wrap">
                    <div class="contact-footer anim-scroll-up" data-aos>
                        <h5 class="title mb-4 text-white">@lang('text.delivery_center')</h5>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <i class="fa-light fa-house text-danger"></i>
                                <span class="text-white">
                                    {{ config('cmsConfig.general.address_2') }}
                                </span>
                            </li>
                            <li>
                                <i class="fa-light fa-envelope text-danger"></i>
                                <a href="#!" class="link text-white">
                                    {{ config('cmsConfig.general.email_2') }}
                                </a>
                            </li>
                            <li>
                                <i class="fa-light fa-phone text-danger"></i>
                                <a href="#!" class="link text-white">
                                    {{ config('cmsConfig.general.phone_2') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bottom-footer text-center">
        <div class="container d-flex flex-column flex-xl-row align-items-center justify-content-between">
            <div class="social-footer d-flex mb-5 mb-xl-0">
                <span class="subtitle text-muted">Follow Us</span>
                <a href="{{ config('cmsConfig.socmed.facebook') }}" class="social-link text-white">
                    <div class="label-btn span-2-red"><i class="fa-brands fa-facebook fs-20"></i></div>
                </a>
                <a href="{{ config('cmsConfig.socmed.instagram') }}" class="social-link text-white">
                    <div class="label-btn span-2-red"><i class="fa-brands fa-instagram fs-20"></i></div>
                </a>
                <a href="{{ config('cmsConfig.socmed.linkedin') }}" class="social-link text-white">
                    <div class="label-btn span-2-red"><i class="fa-brands fa-linkedin fs-20"></i></div>
                </a>
            </div>
            <div class="copyright subtitle text-muted">@lang('text.copyright')</div>
        </div>
    </section>
</footer>
