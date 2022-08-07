@extends('mail.layout')

@section('content')
<h4>{!! $data['title'] !!}</h4>
<p>
    @lang('mail.inquiry_sender.title', [
        'attribute' => $data['webname']
    ])
</p>
<table border="0" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td align="left">
        <table border="0" cellpadding="0" cellspacing="0">
            <tbody>
                {!! $data['inquiry']['mail_sender_template'] !!}
            </tbody>
        </table>
        </td>
    </tr>
    </tbody>
</table>
@endsection
