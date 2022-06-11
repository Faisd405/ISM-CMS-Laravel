<div class="form-group row">
    <div class="col-md-2 text-md-right">
        <label class="col-form-label text-sm-right">{{ ucfirst($data['type']) }} <i class="text-danger">*</i></label>
    </div>
    <div class="col-md-10">
        @if(isset($data['widget']) && $data['widget']['type'] == 'page')
        <input type="hidden" name="moduleable_id" value="{{ $data['widget']['moduleable_id'] }}">
        <input id="moduleable" type="text" class="form-control mb-1" value="{!! $data['widget']['module']['page']->fieldLang('title') !!}" readonly>
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
    <label class="col-form-label col-sm-2 text-sm-right">Child Limit</label>
    <div class="col-sm-10">
        <input type="number" class="form-control mb-1 @error('child_limit') is-invalid @enderror" name="child_limit" 
            value="{{ !isset($data['widget']) ? old('child_limit') : old('child_limit', $data['widget']['content']['child_limit']) }}" 
            placeholder="">
        @include('components.field-error', ['field' => 'child_limit'])
    </div>
</div>