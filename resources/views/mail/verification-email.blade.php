@extends('mail.layout')

@section('content')
<p>Hi <strong>{{ $data['name'] }}</strong>,</p>
<p>@lang('mail.verification_email.text', [
    'attribute' => $data['email']
])</p>
<table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
    <tbody>
    <tr>
        <td align="left">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
            <tr>
                <td> <a href="{{ $data['link'] }}" target="_blank">@lang('mail.verification_email.btn')</a> </td>
            </tr>
            </tbody>
        </table>
        </td>
    </tr>
    </tbody>
</table>
<p>@lang('mail.alert_expired')</p>
@endsection
