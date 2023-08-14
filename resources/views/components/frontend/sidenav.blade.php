@props([
    'hideComponent' => [], // archive, category, platinum, gold

    // archive
    'listArchiveYear' => [],
    'sectionSlug' => '',

    // category
    'section' => [],
    'categories' => [],
])

@if (!in_array('archive', $hideComponent))
    <x-frontend.sidenav.archive-year :listArchiveYear="$listArchiveYear" :sectionSlug="$sectionSlug"></x-frontend.sidenav.archive-year>
@endif

@if (!in_array('category', $hideComponent))
    <x-frontend.sidenav.category :section="$section" :categories="$categories"></x-frontend.sidenav.category>
@endif

@if (!in_array('platinum', $hideComponent))
    <x-frontend.sidenav.sponsor-platinum></x-frontend.sidenav.sponsor-platinum>
@endif

@if (!in_array('gold', $hideComponent))
    <x-frontend.sidenav.sponsor-gold></x-frontend.sidenav.sponsor-gold>
@endif
