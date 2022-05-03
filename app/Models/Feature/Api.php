<?php

namespace App\Models\Feature;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Api extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'feature_apis';
    protected $guarded = [];

    protected $casts = [
        'ip_address' => 'array',
        'modules' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        Api::observe(LogObserver::class);
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
}
