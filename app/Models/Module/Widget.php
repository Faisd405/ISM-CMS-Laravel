<?php

namespace App\Models\Module;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Widget extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'widgets';
    protected $guarded = [];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'content' => 'json',
        'config' => 'json',
        'custom_fields' => 'json',
    ];

    public static function boot()
    {
        parent::boot();

        Widget::observe(LogObserver::class);
    }

    public function moduleable()
    {
        return $this->morphTo();
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

        return $this->hasMany(Widget::class, 'id')->first()[$field][$lang];
    }

    public function scopeGlobal($query)
    {
        return $query->where('global', 1);
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
