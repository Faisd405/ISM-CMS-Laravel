@php
    $langActive = $languages->firstWhere('iso_codes', App::getLocale());
    $langOther = $languages->firstWhere('iso_codes', '!=', App::getLocale());
@endphp