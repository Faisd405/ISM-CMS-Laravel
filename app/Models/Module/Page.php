<?php

namespace App\Models\Module;

use App\Models\IndexingUrl;
use App\Models\Master\Media;
use App\Models\Master\TagType;
use App\Models\Master\Template;
use App\Models\Menu\Menu;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_pages';
    protected $guarded = [];

    protected $casts = [
        'path' => 'json',
        'title' => 'json',
        'intro' => 'json',
        'header_text' => 'json',
        'content' => 'json',
        'cover' => 'json',
        'banner' => 'json',
        'config' => 'json',
        'seo' => 'json',
        'custom_fields' => 'json'
    ];

    protected $appends = [
        'path_parent',
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        Page::observe(LogObserver::class);
    }

    public function childs()
    {
        return $this->hasMany(Page::class, 'parent', 'id');
    }

    public function childRecursive()
    {
        return $this->childs()->with('childs');
    }

    public function getParent()
    {
        return $this->firstWhere('id', $this->parent);
    }

    public function getPathParentAttribute()
    {
        $path = [];

        $slug = '';
        if (!empty($this->path)) {
            foreach ($this->whereIn('id', $this->path)->get() as $key => $value) {
                $path[$key] = $value->slug;
            }

            $getSlug = implode('/', $path);
            $slug = $getSlug.'/'.$this->slug;
        }

        return $slug;
    }

    public function indexing()
    {
        return $this->morphOne(IndexingUrl::class, 'urlable');
    }

    public function menus()
    {
        return $this->morphMany(Menu::class, 'menuable');
    }

    public function widgets()
    {
        return $this->morphMany(Widget::class, 'moduleable');
    }

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function medias()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function tags()
    {
        return $this->morphMany(TagType::class, 'tagable');
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
        if ($lang == null)
            $lang = App::getLocale();

        return $this->hasMany(Page::class, 'id')->first()[$field][$lang];
    }

    public function scopePublish($query)
    {
        return $query->where('publish', 1);
    }

    public function scopePublic($query)
    {
        return $query->where('public', 1);
    }

    public function scopeDetail($query)
    {
        return $query->where('detail', 1);
    }

    public function scopeApproved($query)
    {
        return $query->where('approved', 1);
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }

    public function getCoverSrcAttribute()
    {
        if (!empty($this->cover['filepath'])) {
            $cover = Storage::url($this->cover['filepath']);
        } else {
            if (!empty(config('cmsConfig.file.cover_default'))) {
                $cover = config('cmsConfig.file.cover_default');
            } else {
                $cover = asset(config('cms.files.config.cover_default.file'));
            }
        }

        return $cover;
    }

    public function getBannerSrcAttribute()
    {
        if (!empty($this->banner['filepath'])) {
            $banner = Storage::url($this->banner['filepath']);
        } else {
            if (!empty(config('cmsConfig.file.banner_default'))) {
                $banner = config('cmsConfig.file.banner_default');
            } else {
                $banner = asset(config('cms.files.config.banner_default.file'));
            }
        }

        return $banner;
    }
}
