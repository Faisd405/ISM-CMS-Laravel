<div class="chat-widget dropdown">
    <a href="javascript:;" data-bs-toggle="dropdown" class="btn icon-btn btn-whatsapp">
        <div class="label-btn span-center"><i class="fa-brands fa-whatsapp fs-18"></i></div>
    </a>
    <div class="list-wa dropdown-menu">
        <div class="dropdown-menu-content">
            <ul class="wa-content dropdown-content">
                <li class="wa-header">
                    <div>Silahkan Chat Kami Sekarang !!</div>
                </li>
                <li class="wa-body">
                    <div class="wa-item">
                        <a href="https://web.whatsapp.com/send?phone={{ config('cmsConfig.general.support_phone') }}text={{ config('cmsConfig.general.support_text') }}"
                            target="_blank" class="wa-link dropdown-link d-flex align-items-center">
                            <div class="wa-icon ratio ratio-1x1 rounded-circle overflow-hidden me-3">
                                <div class="thumb d-flex align-items-center justify-content-center">
                                    @if (last(explode('/',config('cmsConfig.file.support_image'))) != null)
                                        <img src="{{ config('cmsConfig.file.support_image') }}" alt=""
                                            class="thumb">
                                    @endif
                                    <i class="fa-brands fa-whatsapp fs-20"></i>
                                </div>
                            </div>
                            <div class="wa-info flex-grow-1">
                                <div class="wa-title fs-12 text-muted">@lang('text.support_1')</div>
                                <div class="wa-name text-dark">{{ config('cmsConfig.general.support_name') }}</div>
                            </div>
                        </a>
                    </div>
                    <div class="wa-item">
                        <a href="https://web.whatsapp.com/send?phone={{ config('cmsConfig.general.support_phone_2') }}text={{ config('cmsConfig.general.support_text_2') }}"
                            target="_blank" class="wa-link dropdown-link d-flex align-items-center">
                            <div class="wa-icon ratio ratio-1x1 rounded-circle overflow-hidden me-3">
                                <div class="thumb d-flex align-items-center justify-content-center">
                                    @if (last(explode('/',config('cmsConfig.file.support_image_2'))) != null)
                                        <img src="{{ config('cmsConfig.file.support_image_2') }}" alt=""
                                            class="thumb">
                                    @endif
                                    <i class="fa-brands fa-whatsapp fs-20"></i>
                                </div>
                            </div>
                            <div class="wa-info flex-grow-1">
                                <div class="wa-title fs-12 text-muted">@lang('text.support_2')</div>
                                <div class="wa-name text-dark">{{ config('cmsConfig.general.support_name_2') }}</div>
                            </div>
                        </a>
                    </div>
                    <div class="wa-item">
                        <a href="https://web.whatsapp.com/send?phone={{ config('cmsConfig.general.support_phone_3') }}text={{ config('cmsConfig.general.support_text_3') }}"
                            target="_blank" class="wa-link dropdown-link d-flex align-items-center">
                            <div class="wa-icon ratio ratio-1x1 rounded-circle overflow-hidden me-3">
                                <div class="thumb d-flex align-items-center justify-content-center">
                                    @if (last(explode('/',config('cmsConfig.file.support_image_3'))) != null)
                                        <img src="{{ config('cmsConfig.file.support_image_3') }}" alt=""
                                            class="thumb">
                                    @endif
                                    <i class="fa-brands fa-whatsapp fs-20"></i>
                                </div>
                            </div>
                            <div class="wa-info flex-grow-1">
                                <div class="wa-title fs-12 text-muted">@lang('text.support_3')</div>
                                <div class="wa-name text-dark">{{ config('cmsConfig.general.support_name_3') }}</div>
                            </div>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
