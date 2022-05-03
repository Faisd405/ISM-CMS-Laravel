<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagType extends Model
{
    use HasFactory;
    
    protected $table = 'master_tag_types';
    protected $guarded = [];

    public $incrementing = false;

    public function tag()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function tagable()
    {
        return $this->morphTo();
    }
}
