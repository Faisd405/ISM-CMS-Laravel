@extends('layouts.frontend.layout')

@section('content')
{{-- DETAIL

    DATA :
    {!! $data['read']->fieldLang('name') !!} // name

    @if ($data['read']['config']['hide_description'] == false)
    {!! $data['read']->fieldLang('description') !!} //description
    @endif

    @if ($data['read']['config']['hide_banner'] == false) //banner
    <img src="{{ $data['banner'] }}" title="{{ $data['read']['banner']['title'] }}" alt="{{ $data['read']['banner']['alt'] }}">
    @endif

    DATA LOOPING :
    $data['read']['categories']
    $data['read']['posts']
    $data['read']['fields']

    {!! $data['creator'] !!}
--}}
<div class="box-wrap banner-breadcrumb">
    <div class="container">
        <div class="banner-content">
            <div class="title-heading">
                <h1>{{ $data['read']->fieldLang('name') }}</h1>
            </div>
            <div class="list-breadcrumb">
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li class="current">
                    {{ $data['read']->fieldLang('name') }}
                    </li>
                </ul>
            </div>
        </div>
        @foreach ($data['read']['categories']->sortBy('position') as $key => $category)
        @if (count($category->posts()->orderBy('position', 'asc')->get()) == 0)
            @continue
        @endif
        <div class="box-section">
            <div class="title-heading">
                <h4><strong>{{ $category->fieldLang('name') }}</strong></h4>
            </div>
            <div class="row justify-content-center">
                @foreach ($category->posts()->orderBy('position', 'asc')->get() as $post)
                <div class="col-md-6 col-lg-4">
                    <div class="contact-branch {{ str_replace("-anggota","",$category->slug) }}">
                        <div class="lable-member">
                            @if (str_contains('member', $category->fieldLang('name')))
                                {{ $category->fieldLang('name') }}
                            @else
                                {{ $category->fieldLang('name') }} Sponsor
                            @endif
                        </div>
                        <div class="title-branch">
                            <h5>{{ $post->fieldLang('title') }}</h5>
                        </div>
                        <div class="content-branch">
                            @if (!empty($post['addon_fields']['address']))
                                <div class="item-branch address">
                                    {{ $post['addon_fields']['address'] ?? '-' }}
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['telephone']))
                                <div class="item-branch telp">
                                    <span>{{ $post['addon_fields']['telephone'] ?? '-' }}</span>
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['email']))
                                <div class="item-branch mail">
                                    <span>{{ $post['addon_fields']['email'] ?? '-' }}</span>
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['website']))
                                <div class="item-branch website">
                                    <a href="">{{ $post['addon_fields']['website'] ?? '-' }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="box-section">
            <div class="title-heading">
                <h4><strong>Member Non Active</strong></h4>
            </div>
            <div class="row justify-content-center">
                @foreach ($data['read']['posts']->filter(function ($item){
                    return in_array(null, $item['category_id']);
                }) as $post)
                <div class="col-md-6 col-lg-4">
                    <div class="contact-branch member-non-active">
                        <div class="lable-member">
                            Member Non Active
                        </div>
                        <div class="title-branch">
                            <h5>{{ $post->fieldLang('title') }}</h5>
                        </div>
                        <div class="content-branch">
                            @if (!empty($post['addon_fields']['address']))
                                <div class="item-branch address">
                                    {{ $post['addon_fields']['address'] ?? '-' }}
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['telephone']))
                                <div class="item-branch telp">
                                    <span>{{ $post['addon_fields']['telephone'] ?? '-' }}</span>
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['email']))
                                <div class="item-branch mail">
                                    <span>{{ $post['addon_fields']['email'] ?? '-' }}</span>
                                </div>
                            @endif
                            @if (!empty($post['addon_fields']['website']))
                                <div class="item-branch website">
                                    <a href="">{{ $post['addon_fields']['website'] ?? '-' }}</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        {{-- <nav aria-label="Page navigation">
            <ul class="pagination">
              <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
      </nav> --}}
    </div>
    <div class="thumbnail-img">
        <img src="{{ $data['read']['cover_src'] }}" alt="">
    </div>
</div>
@endsection
