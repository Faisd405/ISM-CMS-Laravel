<?php

namespace App\Models\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'feature_notifications';
    protected $guarded = [];

    protected $casts = [
        'user_to' => 'array',
        'attribute' => 'json',
        'read_by' => 'array',
    ];

    public function userFrom()
    {
        return $this->belongsTo(User::class, 'user_from');
    }
}
