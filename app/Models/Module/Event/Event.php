<?php

namespace App\Models\Module\Event;

use App\Models\Menu\Menu;
use App\Models\Module\Widget;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Helper;

    protected $table = 'mod_events';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'form_description' => 'json',
        'links' => 'json',
        'email' => 'array',
        'cover' => 'json',
        'banner' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
        'seo' => 'json',
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    protected $appends = [
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        Event::observe(LogObserver::class);
    }

    public function fields()
    {
        return $this->hasMany(EventField::class, 'event_id');
    }

    public function forms()
    {
        return $this->hasMany(EventForm::class, 'event_id');
    }

    public function menus()
    {
        return $this->morphMany(Menu::class, 'menuable');
    }

    public function widgets()
    {
        return $this->morphMany(Widget::class, 'moduleable');
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

        return $this->hasMany(Event::class, 'id')->first()[$field][$lang];
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
