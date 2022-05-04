<html>
<body>

    <table border="1">
        <thead>
            <tr>
                <th colspan="{{ $field->count()+3 }}" style="text-align: center; height:20px;">
                    <h1>{{ strtoupper($event->fieldLang('name')) }}</h1>
                </th>
            </tr>
            <tr>
                <th style="width: 5px;">#</th>
                <th style="width: 25px;">@lang('module/event.form.label.field1')</th>

                @foreach ($field as $item)
                <th style="width: 35px;">{{ $item->fieldLang('label') }}</th>
                @endforeach
                <th style="width: 30px;">@lang('module/event.form.label.field3')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['ip_address'] }}</td>
                @foreach ($field as $field)
                <td>
                    {!! preg_replace("/[^a-zA-Z0-9]/", " ", $item->fields[$field->name]) !!}
                </td>
                @endforeach
                <td>{!! $item['submit_time']->format('d F Y (H:i)') !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>