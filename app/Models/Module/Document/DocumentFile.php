<?php

namespace App\Models\Module\Document;

use App\Models\Feature\Configuration;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class DocumentFile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_document_files';
    protected $guarded = [];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'cover' => 'json',
        'config' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        DocumentFile::observe(LogObserver::class);
    }

    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
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

        return $this->hasMany(DocumentFile::class, 'id')->first()[$field][$lang];
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

    public function coverSrc()
    {
        if (!empty($this->cover['filepath'])) {
            $cover = Storage::url($this->cover['filepath']);
        } else {
            if (!empty(Configuration::value('cover_default'))) {
                $cover = Storage::url(config('cms.files.config.path').
                Configuration::value('cover_default'));
            } else {
                $cover = asset(config('cms.files.config.cover_default.file'));
            }
        }

        return $cover;
    }
}
