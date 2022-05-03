<?php

namespace App\Models\Menu;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'menus';
    protected $guarded = [];

    protected $casts = [
        'title' => 'json',
        'config' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        Menu::observe(LogObserver::class);
    }

    public function menuable()
    {
        return $this->morphTo();
    }

    public function childs()
    {
        return $this->hasMany(Menu::class, 'parent', 'id')->orderBy('position', 'ASC');
    }

    public function childPublish()
    {
        $query = $this->hasMany(Menu::class, 'parent', 'id')->publish()
            ->orderBy('position', 'ASC');
        
        if (Auth::guard()->check() == false)
            $query->public();

        return $query;
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
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

        return $this->hasMany(Menu::class, 'id')->first()[$field][$lang];
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

    public function routes()
    {
        $url = $this->config['url'];
        if (App::getLocale() != config('cms.module.feature.language.default'))
            $url = '/'.App::getLocale().$this->config['url'];

        return $url;
    }
}
