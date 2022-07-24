<div class="form-group row">
    <div class="col-md-2 text-md-right">
        <label class="col-form-label text-sm-right">{{ Str::replace('_', ' ', Str::upper($data['type'])) }} <i class="text-danger">*</i></label>
    </div>
    <div class="col-md-10">
        @if(isset($data['widget']) && $data['widget']['type'] == 'content_section')
        <input type="hidden" name="moduleable_id" value="{{ $data['widget']['moduleable_id'] }}">
        <input id="moduleable" type="text" class="form-control mb-1" value="{!! $data['widget']['module']['section']->fieldLang('name') !!}" readonly>
        @endif
        <select id="moduleable_id" class="select-autocomplete show-tick @error('moduleable_id') is-invalid @enderror" name="moduleable_id" data-style="btn-default">
            <option value="" disabled selected>@lang('global.select')</option>
        </select>
        @error('moduleable_id')
        <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
        @enderror
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.category.caption') Limit</label>
    <div class="col-sm-10">
        <input type="number" class="form-control mb-1 @error('category_limit') is-invalid @enderror" name="category_limit" 
            value="{{ !isset($data['widget']) ? old('category_limit', 4) : old('category_limit', $data['widget']['content']['category_limit']) }}" 
            placeholder="">
        @include('components.field-error', ['field' => 'category_limit'])
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.caption') Limit</label>
    <div class="col-sm-10">
        <input type="number" class="form-control mb-1 @error('post_limit') is-invalid @enderror" name="post_limit" 
            value="{{ !isset($data['widget']) ? old('post_limit', 4) : old('post_limit', $data['widget']['content']['post_limit']) }}" 
            placeholder="">
        @include('components.field-error', ['field' => 'post_limit'])
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.caption') Selected</label>
    <div class="col-sm-10">
        <label class="custom-control custom-checkbox m-0">
            <input type="checkbox" class="custom-control-input" name="post_selected" value="1"
                {{ !isset($data['widget']) ? (old('post_selected') ? 'checked' : '') : (old('post_selected', $data['widget']['content']['post_selected']) ? 'checked' : '') }}>
            <span class="custom-control-label">@lang('global.label.optional.1')</span>
        </label>
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">@lang('module/content.post.caption') Hits</label>
    <div class="col-sm-10">
        <label class="custom-control custom-checkbox m-0">
            <input type="checkbox" class="custom-control-input" name="post_hits" value="1"
                {{ !isset($data['widget']) ? (old('post_hits') ? 'checked' : '') : (old('post_hits', $data['widget']['content']['post_hits']) ? 'checked' : '') }}>
            <span class="custom-control-label">@lang('global.label.optional.1')</span>
        </label>
    </div>
</div>