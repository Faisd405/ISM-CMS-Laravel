<?php

namespace App\Models\Module\Inquiry;

use App\Models\Feature\Configuration;
use App\Models\IndexingUrl;
use App\Models\Menu\Menu;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Inquiry extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_inquiries';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'body' => 'json',
        'after_body' => 'json',
        'banner' => 'json',
        'email' => 'array',
        'config' => 'json',
        'custom_fields' => 'json',
        'seo' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        Inquiry::observe(LogObserver::class);
    }

    public function fields()
    {
        return $this->hasMany(InquiryField::class, 'inquiry_id');
    }

    public function forms()
    {
        return $this->hasMany(InquiryForm::class, 'inquiry_id');
    }

    public function indexing()
    {
        return $this->morphOne(IndexingUrl::class, 'urlable');
    }

    public function menus()
    {
        return $this->morphMany(Menu::class, 'menuable');
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

        return $this->hasMany(Inquiry::class, 'id')->first()[$field][$lang];
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

    public function bannerSrc()
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
