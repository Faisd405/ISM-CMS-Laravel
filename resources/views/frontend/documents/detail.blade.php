@extends('layouts.frontend.layout')

@section('content')
    {{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('name') !!} // name

    @if ($data['read']['config']['hide_description'] == false)
    {!! $data['read']->fieldLang('description') !!} //description
    @endif

    //image preview
    <img src="{{ $data['image_preview'] }}" title="{{ $data['read']['image_preview']['title'] }}" alt="{{ $data['read']['image_preview']['alt'] }}">

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['files']
    $data['read']['fields']

    {!! $data['creator'] !!}
--}}
    <div class="box-wrap banner-breadcrumb">
        <div class="container">
            <div class="banner-content">
                <div class="title-heading">
                    <h1>Publications</h1>
                </div>
                <div class="list-breadcrumb">
                    <ul>
                        <li><a href="">Home</a></li>
                        <li class="current">Publications</a></li>
                    </ul>
                </div>
            </div>
            <div class="row justify-content-center">
                @foreach ($data['files'] as $file)
                    <div class="col-md-6 col-xl-4">
                        <a href="
                        @if ($file['slug']) {{ route('document.file.read', ['slugDocument' => $data['read']['slug'], 'slugFile' => $file['slug']]) }}
                        @else
                        {{ route('document.download', ['id' => $file['id']]) }} @endif
                        "
                            target="_blank" class="magazine">
                            <div class="img-magazine">
                                <img src="{{ $file['cover_src'] }}">
                            </div>
                            <div class="box-btn text-center">
                                <span class="btn btn-primary btn-small">
                                    View Directory
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            @if ($data['read']['config']['paginate_file'])
                <nav aria-label="Page navigation">
                    {{ $data['files']->links('vendor.pagination.frontend') }}
                </nav>
            @endif
        </div>
        <div class="thumbnail-img">
            <img src="{{ $data['read']['cover_src'] }}" alt="">
        </div>
    </div>
@endsection
