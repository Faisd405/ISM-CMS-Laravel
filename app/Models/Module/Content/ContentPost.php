<?php

namespace App\Models\Module\Content;

use App\Models\Master\Media;
use App\Models\Master\TagType;
use App\Models\Master\Template;
use App\Models\Menu\Menu;
use App\Models\User;
use App\Observers\LogObserver;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class ContentPost extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Helper;

    protected $table = 'mod_content_posts';
    protected $guarded = [];

    protected $casts = [
        'category_id' => 'array',
        'title' => 'json',
        'intro' => 'json',
        'content' => 'json',
        'cover' => 'json',
        'banner' => 'json',
        'addon_fields' => 'json',
        'custom_fields' => 'json',
        'config' => 'json',
        'seo' => 'json',
        'publish_time' => 'datetime',
        'publish_end' => 'datetime'
    ];

    protected $appends = [
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        ContentPost::observe(LogObserver::class);
    }

    public function section()
    {
        return $this->belongsTo(ContentSection::class, 'section_id');
    }

    public function categories()
    {
        return ContentCategory::whereIn('id', $this->category_id)->get();
    }

    public function menus()
    {
        return $this->morphMany(Menu::class, 'menuable');
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

        return $this->hasMany(ContentPost::class, 'id')->first()[$field][$lang];
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

    public function scopeSelected($query)
    {
        return $query->where('selected', 1);
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
