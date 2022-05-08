@extends('mail.layout')

@section('content')
<h4>{!! $data['title'] !!}</h4>
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
    @isset($data['request']['email'])  
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
    @endisset
    </tbody>
</table>
@endsection
