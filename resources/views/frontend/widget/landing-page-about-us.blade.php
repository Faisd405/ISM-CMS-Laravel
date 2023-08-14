<div class="box-wrap">
    <div class="container">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-5">
                <div class="about-img">
                    <div class="thumbnail-img">
                        <img src="{{ $widget['module']['page']['cover_src'] }}" alt="">
                    </div>
                </div>
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-center">
                <div class="about-content">
                    <div class="title-heading">
                        <h6>
                            {{ $widget['module']['page']->fieldLang('title') }}
                        </h6>
                        <h2>
                            {{ $widget->fieldLang('title') }}
                        </h2>
                    </div>
                    <article class="summary-content">
                        {!! $widget->fieldLang('description') !!}
                    </article>
                    <div class="box-btn">
                        <a href="{{ route('page.read.' . $widget['module']['page']['slug']) }}"
                            class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
