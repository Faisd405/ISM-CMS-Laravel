<?php

namespace App\Models\Feature;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Language extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'feature_languages';
    protected $guarded = [];

    protected $appends = [
        'url_switcher',
        'flag_icon'
    ];

    public static function boot()
    {
        parent::boot();

        Language::observe(LogObserver::class);
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

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }

    public function getUrlSwitcherAttribute()
    {
        $lang = 'id';
        if (config('cms.module.feature.language.default') == 'id') {
            $lang = 'en';
        }

        $checkUrl = Str::replaceFirst(url('/'), '', URL::full());
        $replaceUrl = Str::replace(url(''), '', URL::full());
        
        if ($checkUrl == '' || $checkUrl == '/'.$lang) {
            $url = Str::replaceFirst($lang, '', Str::replaceFirst($lang.'/', '', $replaceUrl));
        } else {
            $url = Str::replaceFirst('/'.$lang, '', Str::replaceFirst($lang.'/', '', $replaceUrl));
        }
        
        if ($this->iso_codes != config('cms.module.feature.language.default')) {
            $varCode = '';
            if (App::getLocale() != $this->iso_codes) {
                $varCode = '/'.$this->iso_codes;
            }
            $url = Str::replace(url('/'), $varCode, URL::full());
        }

        return $url;
    }

    public function getFlagIconAttribute()
    {
        return asset(config('cms.files.lang').$this->iso_codes.'.svg');
    }
}
