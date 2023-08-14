<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Helper;

class UserLoginFailed extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'failed_time' => 'datetime'
    ];

    public $timestamps = false;
}
