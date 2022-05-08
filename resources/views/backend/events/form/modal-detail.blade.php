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
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
              @foreach ($data['fields'] as $keyF => $field)
              <tr>
                  <th style="width: 240px;">{{ $field->fieldLang('label') }}</th>
                  <td>{!! $item['fields'][$field['name']] !!}</td>
              </tr>
              @endforeach
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal" title="@lang('global.close')">
            <i class="las la-times"></i> @lang('global.close')
          </button>
          @if (isset($item['fields']['email']))
          <a href="mailto:{{ $item['fields']['email'] }}?subject={{ !empty($item['fields']['subject']) ? $item['fields']['subject'] :  $data['event']->fieldLang('name') }}"
            class="btn btn-primary">
            <i class="las la-reply"></i> @lang('global.reply')
          </a>
          @endif
        </div>
      </div>
    </div>
</div>
@endforeach