<?php

namespace App\Models\Module\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventForm extends Model
{
    use HasFactory;

    protected $table = 'mod_event_forms';
    protected $guarded = [];

    protected $casts = [
        'fields' => 'json',
        'submit_time' => 'datetime'
    ];

    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scopeRead($query)
    {
        return $query->where('status', 1);
    }

    public function scopeExport($query)
    {
        return $query->where('exported', 1);
    }
}
