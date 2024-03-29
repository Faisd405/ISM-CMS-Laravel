<html>
<body>

    <table border="1">
        <thead>
            <tr>
                <th colspan="{{ $fields->count()+3 }}" style="text-align: center; height:20px;">
                    <h1>{{ strtoupper($inquiry->fieldLang('name')) }}</h1>
                </th>
            </tr>
            <tr>
                <th style="width: 5px;">#</th>
                <th style="width: 25px;">@lang('module/inquiry.form.label.ip_address')</th>
                @foreach ($fields as $item)
                <th style="width: 35px;">{{ $item->fieldLang('label') }}</th>
                @endforeach
                <th style="width: 30px;">@lang('module/inquiry.form.label.submit_time')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['ip_address'] }}</td>
                @foreach ($fields as $field)
                <td>
                    @php
                        $name = isset($item['fields'][$field['name']]) ? $item['fields'][$field['name']] : '-';
                    @endphp
                    {!! preg_replace("/[^a-zA-Z0-9]/", " ", $name) !!}
                </td>
                @endforeach
                <td>{!! $item['submit_time']->format('d F Y (H:i)') !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>