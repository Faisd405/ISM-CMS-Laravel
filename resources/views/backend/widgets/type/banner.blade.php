<div class="form-group row">
    <div class="col-md-2 text-md-right">
        <label class="col-form-label text-sm-right">{{ ucfirst($data['type']) }} <i class="text-danger">*</i></label>
    </div>
    <div class="col-md-10">
        <select class="select2 show-tick @error('moduleable_id') is-invalid @enderror" name="moduleable_id" data-style="btn-default">
            <option value="" disabled selected>@lang('global.select')</option>
            @foreach ($data['banners'] as $item)
            <option value="{{ $item['id'] }}" 
                {{ isset($data['widget']) ? (old('moduleable_id', $item['id']) == $data['widget']['moduleable_id'] ? 'selected' : '') : '' }}>
                {!! $item->fieldLang('name') !!}
            </option>
            @endforeach
        </select>
        @error('moduleable_id')
        <label class="error jquery-validation-error small form-text invalid-feedback" style="display: inline-block; color:red;">{!! $message !!}</label>
        @enderror
    </div>
</div>
<div class="form-group row">
    <label class="col-form-label col-sm-2 text-sm-right">Banner Limit</label>
    <div class="col-sm-10">
        <input type="number" class="form-control mb-1 @error('banner_limit') is-invalid @enderror" name="banner_limit" 
            value="{{ !isset($data['widget']) ? old('banner_limit') : old('banner_limit', $data['widget']['content']['banner_limit']) }}" 
            placeholder="">
        @include('components.field-error', ['field' => 'banner_limit'])
    </div>
</div>