<div class="box-wrap">
    <div class="container">
        <div class="row">
            <div class="col-xl-3">
                <x-frontend.sidenav.sponsor></x-frontend.sidenav.sponsor>

            </div>
            <div class="col-xl-9">
                <div class="news-section news-update">
                    <div class="title-heading">
                        <h6>IISIA's News</h6>
                        <h2>News Update</h2>
                    </div>
                    <div class="row justify-content-center">
                        @foreach ($widget['module']['posts'] as $post)
                        <div class="col-md-6">
                            <a href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="item-news">
                                <div class="box-img">
                                    <div class="thumbnail-img">
                                        <img src="{{ $post['cover_Src'] }}" alt="">
                                    </div>
                                </div>
                                <div class="box-post">
                                    <div class="post-info">
                                        <span class="post-date">{{ $post['created_at']->format('d M Y') }}</span>
                                        @if ($post->categories()->count() > 0)
                                        <span class="post-cat">
                                            @foreach ($post->categories() as $category)
                                                {{ $category->fieldLang('name') }}
                                            @endforeach
                                        </span>
                                        @endif
                                    </div>

                                    <h4 class="title-post" title="{{ $post->fieldLang('title') }}">
                                        {{ $post->fieldLang('title') }}
                                    </h4>
                                    <article class="summary-content">
                                        {!! $post->fieldLang('intro') !!}
                                    </article>
                                    <div href="{{ route('content.post.read.' . $post->section->slug, ['slugPost' => $post->slug]) }}" class="line-btn justify-content-end">
                                        <span>Read More</span>
                                        <span class="lb-icon"><i class="las la-plus"></i></span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    <div class="box-btn text-center mt-0">
                        <a href="{{ route('content.section.read.'.$widget['module']['section']->slug) }}" class="btn btn-primary">View All Post</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
