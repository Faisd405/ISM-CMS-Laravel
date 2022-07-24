<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndexingUrl extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function urlable()
    {
        return $this->morphTo();
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }
}
