<div class="box-wrap bg-blue">
    <div class="container">
        <div class="row">
            @foreach ($widget['module']->menus()->get() as $menu)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ $menu['module_data']['routes'] }}" class="item-shortcut">
                    <div class="shortcut-content">
                        <i class="{{ $menu['config']['icon'] }}"></i>
                        <span>{{ $menu['module_data']['title'] }}</span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>
