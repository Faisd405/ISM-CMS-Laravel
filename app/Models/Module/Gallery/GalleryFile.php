<?php

namespace App\Models\Module\Gallery;

use App\Models\User;
use App\Observers\LogObserver;
use App\Traits\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class GalleryFile extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Helper;

    protected $table = 'mod_gallery_files';
    protected $guarded = [];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
    ];

    protected $appends = [
        'file_src',
    ];

    public static function boot()
    {
        parent::boot();

        GalleryFile::observe(LogObserver::class);
    }

    public function category()
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }

    public function album()
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
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

        return $this->hasMany(GalleryFile::class, 'id')->first()[$field][$lang];
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

    public function getFileSrcAttribute()
    {
        $type = $this->type;
        $imageType = $this->image_type;
        $videoType = $this->video_type;

        if ($type == '0') {

            if ($imageType == '0') {
                return [
                    'image' => Storage::url(config('cms.files.gallery.path').$this->gallery_album_id.'/'.$this->file),
                    'video' => '',
                ];
            }

            if ($imageType == '1') {
                if ($this->isImageLink($this->file)) {
                    return [
                        'image' => $this->file,
                        'video' => '',
                    ];
                }

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

            $thumbnail = Storage::url(config('cms.files.gallery.thumbnail.path').$this->gallery_album_id.'/'.$this->thumbnail);
            if (empty($this->thumbnail)) {
                if (!empty(config('cmsConfig.file.cover_default'))) {
                    $thumbnail = config('cmsConfig.file.cover_default');
                } else {
                    $thumbnail = asset(config('cms.files.config.cover_default.file'));
                }
            }

            if ($videoType == '0') {

                return [
                    'image' => $thumbnail,
                    'video' => Storage::url(config('cms.files.gallery.path').$this->gallery_album_id.'/'.$this->file),
                ];
            }

            if ($videoType == '1') {
                return [
                    'image' => !empty($this->thumbnail) ? $thumbnail : 'https://i.ytimg.com/vi/'.$this->file.'/mqdefault.jpg',
                    'video' => 'https://www.youtube.com/embed/'.$this->file.'?rel=0;showinfo=0',
                ];
            }
        }
    }
}
