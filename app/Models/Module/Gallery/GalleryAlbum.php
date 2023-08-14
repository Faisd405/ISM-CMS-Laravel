<?php

namespace App\Models\Module\Gallery;

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

class GalleryAlbum extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_gallery_albums';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'cover' => 'json',
        'banner' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
    ];

    protected $appends = [
        'cover_src',
        'banner_src'
    ];

    public static function boot()
    {
        parent::boot();

        GalleryAlbum::observe(LogObserver::class);
    }

    public function category()
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    public function files()
    {
        return $this->hasMany(GalleryFile::class, 'gallery_album_id');
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
        if ($lang == null) {
            $lang = App::getLocale();
        }

        return $this->hasMany(GalleryAlbum::class, 'id')->first()[$field][$lang];
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
        $file = GalleryFile::where('gallery_album_id', $this->id)->first();

        if (!empty($this->cover['filepath'])) {
            $cover = Storage::url($this->cover['filepath']);
        } else {
           
            if (!empty($file)) {

                if ($file['type'] == 0) {
                    if ($file['image_type'] == 0) {
                        $cover = Storage::url(config('cms.files.gallery.path').'/'.$this->id.'/'.
                            $file['file']);
                    }
    
                    if ($file['image_type'] == 1) {
                        $cover = Storage::url($file['file']);
                    }

                    if ($file['image_type'] == 2) {
                        $cover = $file['file'];
                    }
                }

                if ($file['type'] == 1) {
                    
                    $thumbnail = Storage::url(config('cms.files.gallery.thumbnail.path').$file['gallery_album_id'].'/'.$file['thumbnail']);
                    if (empty($file['thumbnail'])) {
                        if (!empty(config('cmsConfig.file.cover_default'))) {
                            $thumbnail = config('cmsConfig.file.cover_default');
                        } else {
                            $thumbnail = asset(config('cms.files.config.cover_default.file'));
                        }
                    }

                    if ($file['video_type'] == '0') {
                        $cover = $thumbnail;
                    }
        
                    if ($file['video_type'] == '1') {
                        $cover = !empty($file['thumbnail']) ? $thumbnail : 'https://i.ytimg.com/vi/'.$file['file'].'/mqdefault.jpg';
                    }

                }
    
            } else {

                if (!empty(config('cmsConfig.file.cover_default'))) {
                    $cover = config('cmsConfig.file.cover_default');
                } else {
                    $cover = asset(config('cms.files.config.cover_default.file'));
                }
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
