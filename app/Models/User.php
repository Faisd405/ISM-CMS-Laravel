<?php

namespace App\Models;

use App\Observers\LogObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPermissions, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active_at' => 'datetime',
        'photo' => 'json'
    ];

    protected $appends = [
        'avatar',
        'is_online'
    ];

    public static function boot()
    {
        parent::boot();

        User::observe(LogObserver::class);
    }

    public function userable()
    {
        return $this->morphTo();
    }

    public function session()
    {
        return $this->hasOne(UserSession::class, 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(UserLog::class, 'user_id');
    }

    public function createBy()
    {
        return User::find($this->created_by);
    }

    public function updateBy()
    {
        return User::find($this->updated_by);
    }

    public function deleteBy()
    {
        return User::find($this->deleted_by);
    }

    public function scopeVerified($query)
    {
        return $query->where('email_verified', 1);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function getAvatarAttribute()
    {
        $photo = $this->photo;

        $path = asset(config('cms.files.avatar.file'));
        if (!empty($photo) && isset($photo['filename']))
            $path = Storage::url(config('cms.files.avatar.path').$photo['filename']);

        return $path;
    }

    public function getIsOnlineAttribute()
    {
        return Cache::has('user-online-'.$this->id);
    }
}
