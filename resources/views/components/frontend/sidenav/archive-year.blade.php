@props([
    'listArchiveYear' => [],
    'sectionSlug' => '',
])

{{-- @if ($listArchiveYear && $sectionSlug) --}}
<div class="item-sidenav">
    <div class="title-heading">
        <h5>Archives</h5>
    </div>
    <x-frontend.archive-year :listArchiveYear="$listArchiveYear" :sectionSlug="$sectionSlug"></x-frontend.archive-year>
</div>
{{-- @endif --}}
