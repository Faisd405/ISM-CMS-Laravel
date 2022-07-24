<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Configuration extends Model
{
    use HasFactory;

    protected $table = 'feature_configurations';
    protected $primaryKey = 'name';
    protected $guarded = [];

    public $incrementing = false;
    public $timestamps = false;

    public function scopeUpload($query)
    {
        return $query->where('is_upload', 1);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeLocked($query)
    {
        return $query->where('locked', 1);
    }

    static function value($name)
    {
        return static::select('value')->firstWhere('name', $name)['value'];
    }

    static function file($name)
    {
        $config = static::select('value')->firstWhere('name', $name);

        if ($config['value'] != null) {
            $file = Storage::url(config('cms.files.config.path').$config['value']);
        } else {
            $file = asset(config('cms.files.config.'.$name.'.file'));
        }

        return $file;
    }
}
