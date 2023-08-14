@extends('layouts.frontend.layout')

@section('content')
{{-- LIST

    DATA :
    $data['banner'] // jika diperlukan banner default
    $data['categories']
    $data['albums']
    $data['files']

    LOOPING :
    $data['categories']
    $data['albums']
    $data['files']

    ATTRIBUTE DIDALAM LOOPING :
    contoh penulisan attribute ada di detail

--}}
<div class="box-wrap banner-breadcrumb">
    <div class="container">
        <div class="banner-content">
            <div class="title-heading">
                <h1>Steel Galleries</h1>
            </div>
            <div class="list-breadcrumb">
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="current">
                        Steel Galleries
                    </li>
                </ul>
            </div>
        </div>
        <div class="row justify-content-center">
            @foreach ($data['albums'] as $album)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('gallery.album.read', ['slugAlbum' => $album['slug']]) }}" class="item-gallery">
                    <div class="box-img">
                        <div class="amount-gallery">
                            <span>
                                {{ count($album['files']) }} Photos
                            </span>
                        </div>
                        <div class="thumbnail-img">
                            <img src="{{ $album['cover_src'] }}" alt="">
                        </div>
                    </div>
                    <div class="box-post">
                        <div class="post-info">
                            <span class="post-date">{{ $album['created_at']->format('d M Y') }}</span>
                            @if ($album->category)
                            <span class="post-cat">
                                {{ $album->category->fieldLang('name') }}
                            </span>
                            @endif
                        </div>
                        <h4 class="title-post">
                           {{ $album->fieldLang('name') }}
                        </h4>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @if ($data['albums']->count() > 0)
            <nav aria-label="Page navigation">
                {{ $data['albums']->links('vendor.pagination.frontend') }}
            </nav>
        @endif
    </div>
    <div class="thumbnail-img">
        <img src="{{  config('cmsConfig.file.cover_default') }}" alt="">
    </div>
</div>
@endsection
