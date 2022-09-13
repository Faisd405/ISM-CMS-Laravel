@foreach ($languages->whereNotIn('iso_codes', App::getLocale()) as $lang) 
{{-- manggil url : {{ $lang['url_switcher'] }} --}}
{{-- manggil icon : {{ $lang['flag_icon'] }} --}}
@endforeach