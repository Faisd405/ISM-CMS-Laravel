@props([
    'listArchiveYear' => [],
    'sectionSlug' => '',
])
@props([
    'listArchiveYear' => [],
    'sectionSlug' => '',
])

{{-- @if ($listArchiveYear && $sectionSlug) --}}
<div class="dropdown-select">
    <a href="#" class="js-link">Archives<i class="las la-folder-open"></i></a>
    <ul class="js-dropdown-list">
        <li><a href="{{ route('content.section.read.' . $sectionSlug) }}">All Archive</a></li>
        @foreach ($listArchiveYear as $year)
            <li>
                <a href="{{ route('content.section.read.' . $sectionSlug, ['filter_post_year' => $year]) }}">
                    {{ $year }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
{{-- @endif --}}
