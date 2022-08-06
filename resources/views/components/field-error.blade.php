@if ($errors->has($field))
<label id="{{ $field.'-error' }}" class="error jquery-validation-error small form-text invalid-feedback" for="{{ $field }}">
    {!! $errors->first($field) !!}
</label>
@endif
