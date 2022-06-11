<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.image')</label>
    <div class="col-sm-10">
        <div class="input-group">
            <input type="text" class="form-control" id="image1" aria-label="Image" aria-describedby="button-image" name="image_file"
                    value="{{ !isset($data['widget']) ? old('image_file') : old('image_file', $data['widget']['content']['image']['filepath']) }}">
            <div class="input-group-append" title="browse file">
                <button class="btn btn-primary file-name" id="button-image" type="button"><i class="las la-image"></i>&nbsp; @lang('global.browse')</button>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-6">
            <input type="text" class="form-control" placeholder="@lang('global.title')" name="image_title" value="{{ !isset($data['widget']) ? old('image_title') : old('image_title', $data['widget']['content']['image']['title']) }}">
            </div>
            <div class="col-sm-6">
            <input type="text" class="form-control" placeholder="@lang('global.alt')" name="image_alt" value="{{ !isset($data['widget']) ? old('image_alt') : old('image_alt', $data['widget']['content']['image']['alt']) }}">
            </div>
        </div>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/widget.label.url')</label>
    <div class="col-sm-10">
        <input type="text" class="form-control mb-1 @error('url') is-invalid @enderror" name="url" 
            value="{{ !isset($data['widget']) ? old('url') : old('url', $data['widget']['content']['url']) }}" 
            placeholder="">
        @include('components.field-error', ['field' => 'url'])
    </div>
</div>