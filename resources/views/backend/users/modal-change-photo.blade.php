<div class="modal fade" id="modal-change-photo">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('profile.photo.change') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="modal-header">
                <h5 class="modal-title">
                    @lang('global.change') 
                    <span class="font-weight-light">@lang('module/user.label.photo')</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    @isset ($data['user']['photo']['filename'])
                    <input type="hidden" name="old_avatar" value="{{ $data['user']['photo']['filename'] }}">
                    @endisset
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFileLang" lang="en" name="avatar">
                        <label class="custom-file-label btn-main" for="customFileLang">
                            @lang('global.browse')
                        </label>
                    </div>
                    @include('components.field-error', ['field' => 'avatar'])
                    <small class="form-text">
                        Allowed : <strong>{{ Str::upper(config('cms.files.avatar.mimes')) }}</strong>.
                        Pixel : <strong>{{ config('cms.files.avatar.pixel') }}</strong>.
                        Max Size : <strong>{{ config('cms.files.avatar.size') }}</strong>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default w-icon" data-dismiss="modal" title="@lang('global.close')">
                    <i class="fi fi-rr-cross-circle"></i> <span>@lang('global.close')</span>
                </button>
                <button type="submit" class="btn btn-main w-icon" title="@lang('global.save_change')">
                    <i class="fi fi-rr-disk"></i> <span>@lang('global.save_change')</span>
                </button>
            </div>
        </form>
    </div>
</div>