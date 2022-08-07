<div class="form-group row {{ isset($data['widget']) && $data['widget']['config']['show_image'] == false ? 'hide-form' : '' }}">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.image')</label>
    <div class="col-sm-10">
        <div class="input-group mb-2">
            <div class="input-group">
                <input type="text" class="form-control text-bolder" id="image1" aria-label="Image" aria-describedby="button-image" name="image_file" placeholder="Browse file..."
                        value="{{ !isset($data['widget']) ? old('image_file') : old('image_file', $data['widget']['content']['image']['filepath']) }}">
                <div class="input-group-append" title="browse file">
                    <button class="btn btn-main file-name w-icon" id="button-image" type="button"><i class="fi fi-rr-folder"></i>&nbsp; @lang('global.browse')</button>
                </div>
            </div>
        </div>
        <div class="input-group">
            <input type="text" class="form-control text-bolder" name="image_title" placeholder="@lang('global.title')"
                value="{{ !isset($data['widget']) ? old('image_title') : old('image_title', $data['widget']['content']['image']['title']) }}">
            <input type="text" class="form-control text-bolder" name="image_alt" placeholder="@lang('global.alt')"
                value="{{ !isset($data['widget']) ? old('image_alt') : old('image_alt', $data['widget']['content']['image']['alt']) }}">
        </div>
    </div>
</div>