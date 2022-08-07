<?php

namespace App\Models\Menu;

use App\Models\Module\Content\ContentCategory;
use App\Models\Module\Content\ContentPost;
use App\Models\Module\Content\ContentSection;
use App\Models\Module\Document\Document;
use App\Models\Module\Event\Event;
use App\Models\Module\Gallery\GalleryAlbum;
use App\Models\Module\Gallery\GalleryCategory;
use App\Models\Module\Inquiry\Inquiry;
use App\Models\Module\Link\Link;
use App\Models\Module\Page;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'menus';
    protected $guarded = [];

    protected $casts = [
        'path' => 'json',
        'title' => 'json',
        'config' => 'json',
    ];

    protected $appends = [
        'module_data'
    ];

    public static function boot()
    {
        parent::boot();

        Menu::observe(LogObserver::class);
    }

    public function menuable()
    {
        return $this->morphTo();
    }

    public function childs()
    {
        return $this->hasMany(Menu::class, 'parent', 'id');
    }

    public function childRecursive()
    {
        return $this->childs()->with('childs');
    }

    public function getParent()
    {
        return $this->firstWhere('id', $this->parent);
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function createBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updateBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleteBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function fieldLang($field, $lang = null)
    {
        if ($lang == null) {
            $lang = App::getLocale();
        }

        return $this->hasMany(Menu::class, 'id')->first()[$field][$lang];
    }

    public function scopePublish($query)
    {
        return $query->where('publish', 1);
    }

    public function scopePublic($query)
    {
        return $query->where('public', 1);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', 1);
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }

    public function getModuleDataAttribute()
    {
        $id = $this->menuable_id;

        if ($this->module == 'page') {
            
            $model = Page::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['title'][App::getLocale()],
                'routes' => route('page.read.'.$model['slug']),
                'active' => '',
                'is_trash' => Page::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'content_section') {
            
            $model = ContentSection::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('content.section.read.'.$model['slug']),
                'active' => '',
                'is_trash' => ContentSection::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'content_category') {
            
            $model = ContentCategory::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('content.category.read.'.$model['section']['slug'], ['slugCategory' => $model['slug']]),
                'active' => '',
                'is_trash' => ContentCategory::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'content_post') {
            
            $model = ContentPost::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['title'][App::getLocale()],
                'routes' => route('content.post.read.'.$model['section']['slug'], ['slugPost' => $model['slug']]),
                'active' => '',
                'is_trash' => ContentPost::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'gallery_category') {
            
            $model = GalleryCategory::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('gallery.category.read', ['slugCategory' => $model['slug']]),
                'active' => '',
                'is_trash' => GalleryCategory::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'gallery_album') {
            
            $model = GalleryAlbum::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('gallery.album.read', ['slugAlbum' => $model['slug']]),
                'active' => '',
                'is_trash' => GalleryAlbum::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'document') {
            
            $model = Document::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('document.category.read', ['slugCategory' => $model['slug']]),
                'active' => '',
                'is_trash' => Document::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'link') {
            
            $model = Link::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('link.category.read', ['slugCategory' => $model['slug']]),
                'active' => '',
                'is_trash' => Link::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'inquiry') {
            
            $model = Inquiry::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('inquiry.read.'.$model['slug']),
                'active' => '',
                'is_trash' => Inquiry::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == 'event') {
            
            $model = Event::withTrashed()->find($id);
            $module = [
                'title' => !empty($this->title[App::getLocale()]) ? $this->title[App::getLocale()] 
                    : $model['name'][App::getLocale()],
                'routes' => route('event.read', ['slugEvent' => $model['slug']]),
                'active' => '',
                'is_trash' => Event::onlyTrashed()->find($id),
            ];
        }

        if ($this->module == null) {

            $url = $this->config['url'];

            if(!filter_var($url, FILTER_VALIDATE_URL)) {
                if (App::getLocale() != config('cms.module.feature.language.default'))
                $url = '/'.App::getLocale().$this->config['url'];

            }

            $module = [
                'title' => $this->title[App::getLocale()],
                'routes' => $url,
                'active' => '',
                'is_trash' => ''
            ];
        }

        return $module;
    }
}
