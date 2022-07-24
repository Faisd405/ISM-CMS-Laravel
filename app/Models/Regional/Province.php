<?php

namespace App\Models\Regional;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'regional_provinces';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        Province::observe(LogObserver::class);
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'province_code', 'code');
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
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

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }
}
