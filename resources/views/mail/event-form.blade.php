@extends('mail.layout')

@section('content')
<p>
    @lang('mail.event.title', [
        'attribute' => $data['request']['name']
    ])
</p>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td align="left">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                @foreach ($data['request'] as $key => $value)
                @if ($key != '_token' && $key != 'g-recaptcha-response')    
                <tr>
                    <th style="width: 200px;">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                    <td>
                        {{ $value != null ? $value : '-' }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        </td>
    </tr>
    <tr>
        <td align="left">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td> <a href="mailto:{{ $data['request']['email'] }}?subject={{ $data['event']->fieldLang('name') }}" target="_blank">@lang('global.reply')</a> </td>
            </tr>
            </tbody>
        </table>
        </td>
    </tr>
    </tbody>
</table>
@endsection
