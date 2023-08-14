<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Helper;
use Illuminate\Support\Str;

class UserLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'content' => 'json'
    ];

    protected $appends = [
        'log_type'
    ];

    public function logable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getLogTypeAttribute()
    {
        $replace1 = Str::replace('mod_', ' ', $this->logable_name);
        $replace2 = Str::replace('_', ' ', $replace1);
        $name = Str::upper($replace2);

        return $name;
    }
}
