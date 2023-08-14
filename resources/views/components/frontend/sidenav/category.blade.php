@props([
    'section' => [],
    'categories' => [],
])

@if ($categories->count() > 0)
<div class="item-sidenav">
    <div class="title-heading">
        <h5>Categories</h5>
    </div>
    <ul>
        @foreach ($categories as $category)
        <li class="menu-sidenav">
            <a href="{{ route('content.category.read.' . $section['slug'], ['slugCategory' => $category['slug']]) }}">
                <span class="sn-title">{{ $category->fieldLang('name') }}</span>
                <span class="sn-icon"><i class="las la-arrow-right"></i></span>
            </a>
        </li>
        @endforeach
    </ul>
</div>
@endif
