<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Helper;

class UserSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'first_access' => 'datetime',
        'last_login' => 'datetime',
        'last_activity' => 'datetime',
        'last_seen' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
