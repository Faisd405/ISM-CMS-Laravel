<?php

namespace App\Models\Module\Banner;

use App\Models\Feature\Configuration;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_banners';
    protected $guarded = [];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'config' => 'json'
    ];

    public static function boot()
    {
        parent::boot();

        Banner::observe(LogObserver::class);
    }

    public function category()
    {
        return $this->belongsTo(BannerCategory::class, 'banner_category_id');
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

        return $this->hasMany(Banner::class, 'id')->first()[$field][$lang];
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

    public function fileSrc()
    {
        $type = $this->type;
        $imageType = $this->image_type;
        $videoType = $this->video_type;

        if ($type == '0') {
            
            if ($imageType == '0') {
                return [
                    'image' => Storage::url(config('cms.files.banner.path').$this->banner_category_id.'/'.$this->file),
                    'video' => '',
                ];
            }

            if ($imageType == '1') {
                return [
                    'image' => Storage::url($this->file),
                    'video'
                ];
            }

            if ($imageType == '2') {
                return [
                    'image' => $this->file,
                    'video' => ''
                ];
            }
        }

        if ($type == '1') {
            
            if ($videoType == '0') {

                $thumbnail = Storage::url(config('cms.files.banner.thumbnail.path').$this->banner_category_id.'/'.$this->thumbnail);
                if (empty($this->thumbnail)) {
                    if (!empty(Configuration::value('cover_default'))) {
                        $thumbnail = Storage::url(config('cms.files.config.path').
                        Configuration::value('cover_default'));
                    } else {
                        $thumbnail = asset(config('cms.files.config.cover_default.file'));
                    }
                }

                return [
                    'image' => $thumbnail,
                    'video' => Storage::url(config('cms.files.banner.path').$this->banner_category_id.'/'.$this->file),
                ];
            }

            if ($videoType == '1') {
                return [
                    'image' => 'https://i.ytimg.com/vi/'.$this->file.'/mqdefault.jpg',
                    'video' => 'https://www.youtube.com/embed/'.$this->file.'?rel=0;showinfo=0',
                ];
            }
        }
    }
}
