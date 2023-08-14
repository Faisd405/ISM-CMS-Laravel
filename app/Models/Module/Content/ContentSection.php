<?php

namespace App\Models\Module\Content;

use App\Models\IndexingUrl;
use App\Models\Master\Template;
use App\Models\Menu\Menu;
use App\Models\Module\Widget;
use App\Models\User;
use App\Observers\LogObserver;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ContentSection extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Helper;

    protected $table = 'mod_content_sections';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'cover' => 'json',
        'banner' => 'json',
        'custom_fields' => 'json',
        'addon_fields' => 'json',
        'config' => 'json',
        'seo' => 'json',
    ];

    protected $appends = [
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        ContentSection::observe(LogObserver::class);
    }

    public function categories()
    {
        return $this->hasMany(ContentCategory::class, 'section_id');
    }

    public function posts()
    {
        return $this->hasMany(ContentPost::class, 'section_id');
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

    public function templateList()
    {
        return $this->belongsTo(Template::class, 'template_list_id');
    }

    public function templateDetailCategory()
    {
        return $this->belongsTo(Template::class, 'template_detail_category_id');
    }

    public function templateDetailPost()
    {
        return $this->belongsTo(Template::class, 'template_detail_post_id');
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

        return $this->hasMany(ContentSection::class, 'id')->first()[$field][$lang];
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

    public function listYearUnique()
    {
        return $this->posts()
            ->distinct()
            ->select('created_at')
            ->pluck('created_at')
            ->map(function ($postDate) {
                return $postDate->format('Y');
            })->unique();
    }

    public function getCoverSrcAttribute()
    {
        if (!empty($this->cover['filepath'])) {
            if ($this->isImageLink($this->cover['filepath'])) {
                return $this->cover['filepath'];
            }

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
            if ($this->isImageLink($this->banner['filepath'])) {
                return $this->banner['filepath'];
            }
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
