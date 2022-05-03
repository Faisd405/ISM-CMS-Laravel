<?php

namespace App\Models\Master;

use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'master_tags';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        Tag::observe(LogObserver::class);
    }

    public function types()
    {
        return $this->hasMany(TagType::class, 'tag_id');
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

    public function scopeFlags($query)
    {
        return $query->where('flags', 1);
    }

    public function scopeStandar($query)
    {
        return $query->where('standar', 1);
    }
}
