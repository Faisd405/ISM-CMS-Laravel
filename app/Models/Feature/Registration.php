<?php

namespace App\Models\Feature;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Registration extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'feature_registrations';
    protected $guarded = [];

    protected $casts = [
        'roles' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    protected $appends = [
        'role_list'
    ];

    public static function boot()
    {
        parent::boot();

        Registration::observe(LogObserver::class);
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

    public function getRoleListAttribute()
    {
        return Role::whereIn('id', $this->roles)->get();
    }
}
