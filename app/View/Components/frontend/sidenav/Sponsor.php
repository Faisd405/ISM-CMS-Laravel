<?php

namespace App\View\Components\frontend\sidenav;

use App\Models\Module\Content\ContentPost;
use App\Models\Module\Content\ContentSection;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sponsor extends Component
{
    public $advertisingPosts;
    public $advertisingContent;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->advertisingContent = ContentSection::where('id', 7)->first();

        $this->advertisingPosts = ContentPost::where('section_id', 7)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.frontend.sidenav.sponsor');
    }
}
