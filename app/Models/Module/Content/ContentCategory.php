<?php

namespace App\Models\Module\Content;

use App\Models\Master\Template;
use App\Models\Menu\Menu;
use App\Models\Module\Widget;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ContentCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_content_categories';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'cover' => 'json',
        'banner' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
        'seo' => 'json',
    ];

    protected $appends = [
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        ContentCategory::observe(LogObserver::class);
    }

    public function section()
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }

    public function posts()
    {
        return ContentPost::whereJsonContains('category_id', ''.$this->id.'');
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

        return $this->hasMany(ContentCategory::class, 'id')->first()[$field][$lang];
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
