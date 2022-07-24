<?php

namespace App\Models\Module\Banner;

use App\Models\Module\Widget;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Banner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'mod_banners';
    protected $guarded = [];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        Banner::observe(LogObserver::class);
    }

    public function files()
    {
        return $this->hasMany(BannerFile::class, 'banner_id');
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
}
