<?php

namespace App\Models\Master;

use App\Models\Module\Content\ContentCategory;
use App\Models\Module\Content\ContentPost;
use App\Models\Module\Content\ContentSection;
use App\Models\Module\Document\DocumentCategory;
use App\Models\Module\Gallery\GalleryAlbum;
use App\Models\Module\Gallery\GalleryCategory;
use App\Models\Module\Link\LinkCategory;
use App\Models\Module\Page;
use App\Models\User;
use App\Observers\LogObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'master_templates';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        Template::observe(LogObserver::class);
    }

    public function pages()
    {
        return $this->hasMany(Page::class, 'template_id');
    }

    public function contentSectionLists()
    {
        return $this->hasMany(ContentSection::class, 'template_list_id');
    }

    public function contentSectionDetails()
    {
        return $this->hasMany(ContentSection::class, 'template_detail_id');
    }

    public function contentCategories()
    {
        return $this->hasMany(ContentCategory::class, 'template_id');
    }

    public function contentPosts()
    {
        return $this->hasMany(ContentPost::class, 'template_id');
    }

    public function galleryCategoryLists()
    {
        return $this->hasMany(GalleryCategory::class, 'template_list_id');
    }

    public function galleryCategoryDetails()
    {
        return $this->hasMany(GalleryCategory::class, 'template_detail_id');
    }

    public function galleryAlbums()
    {
        return $this->hasMany(GalleryAlbum::class, 'template_id');
    }

    public function documentCategories()
    {
        return $this->hasMany(DocumentCategory::class, 'template_id');
    }

    public function linkCategories()
    {
        return $this->hasMany(LinkCategory::class, 'template_id');
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
}
