<?php

namespace App\Models\Module\Content;

use App\Models\Feature\Configuration;
use App\Models\IndexingUrl;
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

class ContentSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_content_sections';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'banner' => 'json',
        'custom_fields' => 'json',
        'addon_fields' => 'json',
        'config' => 'json',
        'seo' => 'json',
    ];

    protected $appends = [
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

    public function templateDetail()
    {
        return $this->belongsTo(Template::class, 'template_detail_id');
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

    public function getBannerSrcAttribute()
    {
        if (!empty($this->banner['filepath'])) {
            $banner = Storage::url($this->banner['filepath']);
        } else {
            if (!empty(Configuration::value('banner_default'))) {
                $banner = Storage::url(config('cms.files.config.path').
                Configuration::value('banner_default'));
            } else {
                $banner = asset(config('cms.files.config.banner_default.file'));
            }
        }

        return $banner;
    }
}
