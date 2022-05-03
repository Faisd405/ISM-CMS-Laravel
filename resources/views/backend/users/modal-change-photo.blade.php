<div class="modal fade" id="modal-change-photo">
    <div class="modal-dialog">
      <form class="modal-content" action="{{ route('profile.photo.change') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">
            @lang('global.change')
            <span class="font-weight-light">@lang('module/user.label.photo')</span>
            {{-- <br>
            <small class="text-muted">form is required & name is unique</small> --}}
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
        </div>
        <div class="modal-body">
            <div class="form-row">
              <div class="form-group col">
                <label class="form-label">@lang('module/user.label.photo') <i class="text-danger">*</i></label>
                <label class="custom-file-label" for="upload-2"></label>
                @isset ($data['user']['photo']['filename'])
                <input type="hidden" name="old_avatars" value="{{ $data['user']['photo']['filename'] }}">
                @endisset
                <input class="form-control custom-file-input file @error('avatars') is-invalid @enderror" type="file" id="upload-2" lang="en" name="avatars">
                @include('components.field-error', ['field' => 'avatars'])
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" title="@lang('global.close')">
            <i class="las la-times"></i> @lang('global.close')
          </button>
          <button type="submit" class="btn btn-primary" title="@lang('global.save_change')">
            <i class="las la-save"></i> @lang('global.save_change')
          </button>
        </div>
      </form>
    </div>
</div>
