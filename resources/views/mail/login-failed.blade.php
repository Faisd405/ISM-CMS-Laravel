@extends('mail.layout')

@section('content')
<p>@lang('mail.login_failed.text')</p>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td align="left">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                <tr>
                    <th style="width: 210px;">IP Address</th>
                    <td style="width: 10px;">:</td>
                    <td>{{ $data['ip_address'] }}</td>
                </tr>
                <tr>
                    <th style="width: 210;">Username</th>
                    <td style="width: 10px;">:</td>
                    <td>{{ $data['username'] }}</td>
                </tr>
                <tr>
                    <th style="width: 210;">Password</th>
                    <td style="width: 10px;">:</td>
                    <td>{{ $data['password'] }}</td>
                </tr>
            </tbody>
        </table>
        </td>
    </tr>
    </tbody>
</table>
@endsection
