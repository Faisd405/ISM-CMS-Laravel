@extends('layouts.frontend.layout')

@if (config('cms.setting.recaptcha') == true)
    @section('jshead')
        {!! htmlScriptTagJsApi() !!}
    @endsection
@endif

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('title') !!} // title

    @if ($data['read']['config']['hide_body'] == false)
    {!! $data['read']->fieldLang('body') !!} //body
    @endif

    {!! $data['read']->fieldLang('after_body') !!} //after_body

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['fields']
    $data['read']['custom_fields']

    {!! $data['creator'] !!}

--}}
    <div id="map"></div>
    <div class="box-wrap no-overflow">
        <div class="container">
            <div class="box-content box-inquiry">
                <div class="row justify-content-between">
                    <div class="col-xl-4">
                        <div class="contact-desc top">
                            <h5>The Indonesian Iron and Steel Industry Association (IISIA)</h5>
                            <br>
                            <h5>Our Location</h5>
                            <p>Wisma Baja Lt 9 Krakatau Steel Building Jl. Jend. Gatot Subroto Kav 54 Jakarta Selatan</p>
                            <br>
                            <h5>Quick Contact</h5>
                            <p>admin : admin@admin.com</p>
                            <p>info : info@info.com</p>
                            <br>
                            <h5>Let's Talk</h5>
                            <p>phone 1 : 021-321 456 9780</p>
                            <p>phone 2 : 021-321 456 9780</p>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <div class="contact-inquiry">
                            <div class="title-heading text-left">
                                <h1>Form Contact</h1>
                            </div>
                            @if (session('success') || Cookie::get($data['read']['slug']))
                                <div class="text-center">
                                    <h2>
                                        <b>
                                            Message has been sent
                                        </b>
                                    </h2>
                                    <article>
                                        {!! $data['read']->fieldLang('after_body') !!}
                                    </article>
                                </div>
                            @else
                            <form action="{{ route('inquiry.submit', ['id' => $data['read']->id]) }}" method="post">
                                @csrf
                                <div class="row">
                                    @foreach ($data['read']['fields'] as $field)

                                    @if ($field->type == '1')
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <textarea id="{{ $field->name }}" name="{{ $field->name }}" class="form-control" placeholder="{{ $field->fieldLang('placeholder') }}"
                                                @if (isset($field->validation))
                                                @foreach ($field->validation as $validation)
                                                {{ $validation }}
                                                @endforeach
                                                @endif
                                                ></textarea>
                                                @error($field->name)
                                                    <div class="text text-danger">{{ $message }}</div>
                                                @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input id="{{ $field->name }}" name="{{ $field->name }}" class="form-control" type="{{ $field['properties']['type'] }}"
                                                placeholder="{{ $field->fieldLang('placeholder') }}"
                                                @if (isset($field->validation))
                                                @foreach ($field->validation as $validation)
                                                {{ $validation }}
                                                @endforeach
                                                @endif
                                                >
                                                @error($field->name)
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                    <div class="col-md-12 text-right">
                                        <button class="btn btn-primary" type="submit">Send Message</button>
                                    </div>
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jsbody')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB0CuuQ5YQNoIc91Ser9cbum8gYy0oOf4w&callback=initMap" async
        defer></script>
    <script src="{{ asset('assets/frontend/js/google_map.js') }}"></script>
@endsection
