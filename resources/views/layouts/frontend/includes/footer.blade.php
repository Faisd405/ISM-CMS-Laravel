
<footer>
    <div class="footer">
        <div class="f-top">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="f-widget">
                            <h5 class="f-title">
                                Our Office
                            </h5>
                            <div class="f-list-nav">
                                <ul>
                                    <li>
                                        <i class="las la-map-marker-alt"></i>
                                        <span>{{ config('cmsConfig.general.address') }}</span>
                                    </li>
                                    <li>
                                        <i class="las la-phone-volume"></i>
                                        <span>{{ config('cmsConfig.general.phone') }}</span>
                                    </li>
                                    <li>
                                        <a href="mailto:{{ config('cmsConfig.general.email') }}, {{ config('cmsConfig.general.email_2') }}"  class="border-btm">
                                            <i class="las la-at"></i>
                                            <span>
                                                {{ config('cmsConfig.general.email') }}, {{ config('cmsConfig.general.email_2') }}
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="f-widget">
                            <h5 class="f-title">
                                Quick Links
                            </h5>
                            <div class="f-list-nav">
                                <ul class="menu-quicks">
                                    @foreach ($menu['footer_quick_link'] as $footer)
                                    <li><a href="{{ $footer['module_data']['routes'] }}">{{ $footer['module_data']['title'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="f-widget">
                            <h5 class="f-title">
                                Our Partners
                            </h5>
                            <div class="f-list-nav">
                                <ul>
                                    @foreach ($menu['footer_our_partner'] as $footer)
                                    <li><a href="{{ $footer['module_data']['routes'] }}">{{ $footer['module_data']['title'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="f-widget pr-0">
                            <h5 class="f-title">
                                IISIA News
                            </h5>
                            <form>
                                <div class="form-group">
                                    <label for="subscribe">Want to hear news from us?</label>
                                    <input placeholder="Your Email Address" id="subscribe" class="form-control" type="email">
                                    <button class="btn btn-icon-abslt" type="submit"><i class="las la-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="f-bottom">
            <div class="container">
                <div class="f-bottom-flex">
                    <div class="f-widget copyright">
                        <span>2020, IISIA. All Rights Reserved. developed by</span>
                        <a href="http://www.4visionmedia.com/" target="_blank" class="logo-4vm"><img src="{{ asset('assets/frontend/images/logo-4vm.svg') }}" alt=""></a>
                    </div>
                    <div class="f-widget socmed">
                        <ul>
                            @if (config('cmsConfig.socmed.facebook'))
                                <li><a href="{{ config('cmsConfig.socmed.facebook') }}"><i class="lab la-facebook-f"></i></a></li>
                            @endif
                            @if (config('cmsConfig.socmed.instagram'))
                                <li><a href="{{ config('cmsConfig.socmed.instagram') }}"><i class="lab la-instagram"></i></a></li>
                            @endif
                            @if (config('cmsConfig.socmed.twitter'))
                                <li><a href="{{ config('cmsConfig.socmed.twitter') }}"><i class="lab la-twitter"></i></a></li>
                            @endif
                            @if (config('cmsConfig.socmed.youtube'))
                                <li><a href="{{ config('cmsConfig.socmed.youtube') }}"><i class="lab la-youtube"></i></a></li>
                            @endif
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</footer>
