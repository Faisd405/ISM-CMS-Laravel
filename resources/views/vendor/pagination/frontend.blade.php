@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item"><a href="#!"
                    class="page-link page-prev btn icon-btn rounded-circle btn-light-warning disabled">
                    <div class="label-btn label-btn-left"><i class="fa-regular fa-arrow-left"></i></div>
                </a></li>
        @else
            <li class="page-item"><a href="{{ $paginator->previousPageUrl() }}"
                    class="page-link page-prev btn icon-btn rounded-circle btn-light-warning">
                    <div class="label-btn label-btn-left"><i class="fa-regular fa-arrow-left"></i></div>
                </a></li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><a href="#">{{ $element }}</a></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active"><a class="page-link btn icon-btn rounded-circle"
                                href="#">{{ $page }}</a></li>
                    @else
                        <li class="page-item"><a class="page-link btn icon-btn rounded-circle"
                                href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item"><a href="{{ $paginator->nextPageUrl() }}"
                    class="page-link page-next btn icon-btn rounded-circle btn-light-warning">
                    <div class="label-btn label-btn-right"><i class="fa-regular fa-arrow-right"></i></div>
                </a></li>
        @else
            <li class="page-item"><a href="#!"
                    class="page-link page-next btn icon-btn rounded-circle btn-light-warning disabled">
                    <div class="label-btn label-btn-right"><i class="fa-regular fa-arrow-right"></i></div>
                </a></li>
        @endif
    </ul>

@endif
