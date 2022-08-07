@foreach ($data['forms'] as $item)
<div class="modal fade" id="modal-read-{{ $item->id }}">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            @lang('global.detail')
            <span class="font-weight-light">@lang('module/event.form.caption')</span>
            {{-- <br>
            <small class="text-muted">form is required & name is unique</small> --}}
          </h5>
          <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close"><i class="fi fi-rr-cross-small"></i></button>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
              @foreach ($data['fields'] as $keyF => $field)
              <tr>
                  <th style="width: 240px;">{{ $field->fieldLang('label') }}</th>
                  <td>{!! isset($item['fields'][$field['name']]) ? $item['fields'][$field['name']] : '-' !!}</td>
              </tr>
              @endforeach
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default w-icon" data-dismiss="modal" title="@lang('global.close')">
            <i class="fi fi-rr-cross-circle"></i> <span>@lang('global.close')</span>
          </button>
          @if (isset($item['fields']['email']))
          <a href="mailto:{{ $item['fields']['email'] }}?subject={{ !empty($item['fields']['subject']) ? $item['fields']['subject'] :  $data['event']->fieldLang('name') }}"
            class="btn btn-main w-icon">
            <i class="fi fi-rr-arrows"></i> <span>@lang('global.reply')</span>
          </a>
          @endif
        </div>
      </div>
    </div>
</div>
@endforeach