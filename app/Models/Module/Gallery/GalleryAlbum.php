<?php

namespace App\Models\Module\Gallery;

use App\Models\Feature\Configuration;
use App\Models\Master\Template;
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
        'image_preview' => 'json',
        'banner' => 'json',
        'custom_fields' => 'json',
        'config' => 'json',
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

    public function scopeApproved($query)
    {
        return $query->where('approved', 1);
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }

    public function imgPreview()
    {
        $file = GalleryFile::where('gallery_album_id', $this->id)->first();

        if (!empty($this->image_preview['filepath'])) {
            $preview = Storage::url($this->image_preview['filepath']);
        } else {
           
            if (!empty($file)) {

                $preview = Storage::url(config('cms.files.gallery.path').'/'.$this->id.'/'.
                    $file['file']);
    
            } else {

                if (!empty(Configuration::value('cover_default'))) {
                    $preview = Storage::url(config('cms.files.config.path').
                    Configuration::value('cover_default'));
                } else {
                    $preview = asset(config('cms.files.config.cover_default.file'));
                }
            }
    
        }        
        
        return $preview;
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
