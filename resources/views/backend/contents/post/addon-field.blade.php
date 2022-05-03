@foreach ($section['addon_fields'] as $key => $val)
<div class="form-group row">
    @switch($val['type'])
        @case('textarea')
            <label class="col-form-label col-sm-2 text-sm-right">{{ $val['value'] }}</label>
            <div class="col-sm-10">
                <textarea class="form-control" name="af_{{ $val['name'] }}" placeholder="{{ $val['value'] }}">{{ isset($post) && isset($post['addon_fields'][$val['name']]) ? old($val['name'], $post['addon_fields'][$val['name']]) : old($val['name']) }}</textarea>
            </div>
            @break
        @case('date')
            <label class="col-form-label col-sm-2 text-sm-right">{{ $val['value'] }}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text" class="form-control datepicker" name="af_{{ $val['name'] }}" 
                        value="{{ isset($post) && isset($post['addon_fields'][$val['name']]) ? old($val['name'], $post['addon_fields'][$val['name']]) : old($val['name'])}}" placeholder="{{ $val['value'] }}" readonly>
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="las la-calendar"></i></span>
                    </div>
                </div>
            </div>
            @break
        @case('checkbox')
            @php
                $checkbox = json_decode($val['value'], true);
            @endphp
            <label class="col-form-label col-sm-2 text-sm-right">{{ $checkbox[0] }}</label>
            <div class="col-sm-10">
                <div>
                    @foreach ($checkbox[1] as $keyC => $valC)
                    <label class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="af_{{ $val['name'] }}[{{ $keyC }}]" value="{{ $keyC }}" 
                            {{ isset($post) && isset($post['addon_fields'][$val['name']][$keyC]) ? ($keyC == $post['addon_fields'][$val['name']][$keyC] ? 'checked' : '') : '' }}>
                        <span class="form-check-label">
                        {{ $valC }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @break
        @default
        <label class="col-form-label col-sm-2 text-sm-right">{{ $val['value'] }}</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="af_{{ $val['name'] }}" 
                value="{{ isset($post) && isset($post['addon_fields'][$val['name']]) ? old($val['name'], $post['addon_fields'][$val['name']]) : old($val['name']) }}" 
                placeholder="{{ $val['value'] }}">
        </div>
            
    @endswitch
</div>
@endforeach