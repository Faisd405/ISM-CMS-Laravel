<?php

namespace App\Services\Module;

use App\Models\Module\Banner\BannerCategory;
use App\Models\Module\Content\ContentCategory;
use App\Models\Module\Content\ContentSection;
use App\Models\Module\Document\DocumentCategory;
use App\Models\Module\Event\Event;
use App\Models\Module\Gallery\GalleryAlbum;
use App\Models\Module\Gallery\GalleryCategory;
use App\Models\Module\Inquiry\Inquiry;
use App\Models\Module\Link\LinkCategory;
use App\Models\Module\Page;
use App\Models\Module\Widget;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class WidgetService
{
    use ApiResponser;

    private $widgtModel, $language;

    public function __construct(
        Widget $widgtModel,
        LanguageService $language
    )
    {
        $this->widgtModel = $widgtModel;
        $this->language = $language;
    }

    /**
     * Get Widget List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getWidgetList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $widget = $this->widgtModel->query();

        if ($isTrash == true)
            $widget->onlyTrashed();

        if (isset($filter['widget_set']))
            $widget->where('widget_set', $filter['widget_set']);

        if (isset($filter['type']))
            $widget->where('type', $filter['type']);

        if (isset($filter['publish']))
            $widget->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $widget->where('public', $filter['public']);

        if (isset($filter['approved']))
            $widget->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $widget->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $widget->when($filter['q'], function ($widget, $q) {
                $widget->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $widget->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $widget->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $widget->paginate($limit);
        } else {
            $result = $widget->get();
        }

        return $result;
    }

    /**
     * Get Widget One
     * @param array $where
     * @param array $with
     */
    public function getWidget($where, $with = [])
    {
        $widget = $this->widgtModel->query();

        if (!empty($with))
            $widget->with($with);

        $result = $widget->firstWhere($where);

        return $result;
    }

    /**
     * get Module Data
     * @param model $data
     */
    public function getModuleData($data)
    {
        $module = [];

        if (isset($data['content']['ordering'])) {
            if ($data['content']['ordering'] == 'publish_time') {
                $orderBy['publish_time'] = 'DESC';
            } elseif ($data['content']['ordering'] == 'position') {
                $orderBy['position'] = 'ASC';
            } else {
                $orderBy = [];
            }
        }

        if ($data['type'] == 'page') {
            $filter['parent'] = $data['moduleable_id'];
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $module = [
                'page' => App::make(PageService::class)->getPage(['id' => $data['moduleable_id']]),
                'childs' => App::make(PageService::class)->getPageList($filter, true, $data['content']['child_limit'], false, [], $orderBy)
            ];
        }

        if ($data['type'] == 'content_section') {
            $module['section'] = App::make(ContentService::class)->getSection(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['section_id'] = $data['moduleable_id'];
            $module['categories'] = App::make(ContentService::class)->getCategoryList($filter, true, $data['content']['category_limit'], false, [], $orderBy);

            if ($data['content']['post_selected']) {
                $filter['selected'] = 1;
            }

            if ($data['content']['post_hits']) {
                $orderBy['hits'] ='DESC';
            }

            $module['posts'] = App::make(ContentService::class)->getPostList($filter, true, $data['content']['post_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'content_category') {
            $module['category'] = App::make(ContentService::class)->getCategory(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['section_id'] = $data['moduleable_id'];
            if ($data['content']['post_selected']) {
                $filter['selected'] = 1;
            }

            if ($data['content']['post_hits']) {
                $orderBy['hits'] ='DESC';
            }

            $module['posts'] = App::make(ContentService::class)->getPostList($filter, true, $data['content']['post_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'banner') {
            $module['category'] = App::make(BannerService::class)->getCategory(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['banner_category_id'] = $data['moduleable_id'];

            $limit = $module['category']['banner_perpage'];
            if ($data['content']['banner_limit'] > 0) {
                $limit = $data['content']['banner_limit'];
            }

            $module['banners'] = App::make(BannerService::class)->getBannerList($filter, true, $limit, false, [], $orderBy);
        }

        if ($data['type'] == 'gallery_category') {
            $module['category'] = App::make(GalleryService::class)->getCategory(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['gallery_category_id'] = $data['moduleable_id'];

            $module['albums'] = App::make(GalleryService::class)->getAlbumList($filter, true, $data['content']['album_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'gallery_album') {
            $module['album'] = App::make(GalleryService::class)->getAlbum(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['gallery_album_id'] = $data['moduleable_id'];
            
            $module['files'] = App::make(GalleryService::class)->getFileList($filter, true, $data['content']['file_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'document') {
            $module['category'] = App::make(DocumentService::class)->getCategory(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['documents_category_id'] = $data['moduleable_id'];
            
            $module['files'] = App::make(DocumentService::class)->getFileList($filter, true, $data['content']['file_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'link') {
            $module['category'] = App::make(LinkService::class)->getCategory(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['link_category_id'] = $data['moduleable_id'];
            
            $module['medias'] = App::make(LinkService::class)->getMediaList($filter, true, $data['content']['media_limit'], false, [], $orderBy);
        }

        if ($data['type'] == 'inquiry') {
            $module['inquiry'] = App::make(InquiryService::class)->getInquiry(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['inquiry_id'] = $data['moduleable_id'];
            
            $module['fields'] = App::make(InquiryService::class)->getFieldList($filter, false, 0, false, [], $orderBy);
        }

        if ($data['type'] == 'event') {
            $module['event'] = App::make(EventService::class)->getEvent(['id' => $data['moduleable_id']]);
            
            $filter['publish'] = 1;
            $filter['approved'] = 1;
            $filter['event_id'] = $data['moduleable_id'];
            
            $module['fields'] = App::make(EventService::class)->getFieldList($filter, false, 0, false, [], $orderBy);
        }

        return $module;
    }

    /**
     * Create Widget
     * @param array $data
     */
    public function storeWidget($data)
    {
        try {

            $setPath = 'views/frontend/widget/'.Str::slug($data['template'], '-').'.blade.php';
            if (file_exists(resource_path($setPath))) {
                return $this->error(null,  __('module/widget.alert.file_exist'));
            }

            $widget = new Widget;
            $this->setFielWidget($data, $widget);
            $widget->type = $data['type'];
            $widget->template = Str::slug($data['template'], '-');
            $widget->position = $this->widgtModel->max('position') + 1;

            if (Auth::guard()->check())
                if (Auth::user()->hasRole('support|admin|editor') && config('module.widget.approval') == true) {
                    $widget->approved = 2;
                }
                $widget->created_by = Auth::user()['id'];

            $widget->save();

            if (!file_exists(resource_path($setPath))) {
                $path = resource_path($setPath);
                File::put($path, '');
            }

            return $this->success($widget,  __('global.alert.create_success', [
                'attribute' => __('module/widget.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Widget
     * @param array $data
     * @param array $where
     */
    public function updateWidget($data, $where)
    {
        $widget = $this->getWidget($where);
        $oldTemplate = $widget['template'];

        try {
            
            $widget->template = Str::slug($data['template'], '-');
            $this->setFielWidget($data, $widget);
            $widget->save();

            if ($oldTemplate != $widget['template']) {
                
                File::copy(resource_path('views/frontend/widget/'.$oldTemplate.'.blade.php'), 
                    resource_path('views/frontend/widget/'.$widget['template'].'.blade.php'));

                $path = resource_path('views/frontend/widget/'.$oldTemplate.'.blade.php');
                File::delete($path);
            }

            return $this->success($widget,  __('global.alert.update_success', [
                'attribute' => __('module/widget.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Widget
     * @param array $data
     * @param model $widget
     */
    private function setFielWidget($data, $widget)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];
            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];
        }
        
        $widget->name = $name;
        $widget->description = $description;
        $widget->widget_set = $data['widget_set'];

        if ($data['type'] != 'text') {
            $this->moduleAssociate($data, $widget);
        }

        if ($data['type'] == 'text') {
            $widget->content = [
                'image' => [
                    'filepath' => Str::replace(url('/storage'), '', $data['image_file']) ?? null,
                    'title' => $data['image_title'] ?? null,
                    'alt' => $data['image_alt'] ?? null,
                ],
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'page') {
            $widget->content = [
                'child_limit' => $data['child_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'content_section') {
            $widget->content = [
                'category_limit' => $data['category_limit'] ?? 0,
                'post_limit' => $data['post_limit'] ?? 0,
                'post_selected' => isset($data['post_selected']) ? (bool)$data['post_selected'] : false,
                'post_hits' => isset($data['post_hits']) ? (bool)$data['post_hits'] : false,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'content_category') {
            $widget->content = [
                'post_limit' => $data['post_limit'] ?? 0,
                'post_selected' => isset($data['post_selected']) ? (bool)$data['post_selected'] : false,
                'post_hits' => isset($data['post_hits']) ? (bool)$data['post_hits'] : false,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'banner') {
            $widget->content = [
                'banner_limit' => $data['banner_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'gallery_category') {
            $widget->content = [
                'album_limit' => $data['album_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'gallery_album') {
            $widget->content = [
                'file_limit' => $data['file_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'document') {
            $widget->content = [
                'file_limit' => $data['file_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'link') {
            $widget->content = [
                'media_limit' => $data['media_limit'] ?? 0,
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'inquiry') {
            $widget->content = [
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        if ($data['type'] == 'event') {
            $widget->content = [
                'url' => $data['url'],
                'ordering' => $data['ordering'],
            ];
        }

        $widget->global = (bool)$data['global'];
        $widget->publish = (bool)$data['publish'];
        $widget->public = (bool)$data['public'];
        $widget->locked = (bool)$data['locked'];

        return $widget;
    }

    /**
     * Module Associate
     *  @param array $data
     *  @param model $widget
     */
    private function moduleAssociate($data, $widget)
    {
        if ($data['type'] == 'page') {
            $module = Page::find($data['moduleable_id']);
        }

        if ($data['type'] == 'content_section') {
            $module = ContentSection::find($data['moduleable_id']);
        }

        if ($data['type'] == 'content_category') {
            $module = ContentCategory::find($data['moduleable_id']);
        }

        if ($data['type'] == 'banner') {
            $module = BannerCategory::find($data['moduleable_id']);
        }

        if ($data['type'] == 'gallery_category') {
            $module = GalleryCategory::find($data['moduleable_id']);
        }

        if ($data['type'] == 'gallery_album') {
            $module = GalleryAlbum::find($data['moduleable_id']);
        }

        if ($data['type'] == 'document') {
            $module = DocumentCategory::find($data['moduleable_id']);
        }

        if ($data['type'] == 'link') {
            $module = LinkCategory::find($data['moduleable_id']);
        }

        if ($data['type'] == 'inquiry') {
            $module = Inquiry::find($data['moduleable_id']);
        }

        if ($data['type'] == 'event') {
            $module = Event::find($data['moduleable_id']);
        }

        return $widget->moduleable()->associate($module);
    }

    /**
     * Status Widget (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusWidget($field, $where)
    {
        $widget = $this->getWidget($where);

        try {
            
            $widget->update([
                $field => !$widget[$field],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $widget['updated_by'],
            ]);

            return $this->success($widget, __('global.alert.update_success', [
                'attribute' => __('module/widget.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Set Position Widgeet
     * @param array $where
     * @param int $position
     */
    public function positionWidget($where, $position)
    {
        $widget = $this->getWidget($where);
        
        try {

            if ($position >= 1) {
    
                $this->widgtModel->where('position', $position)->update([
                    'position' => $widget['position'],
                ]);
    
                $widget->position = $position;
                if (Auth::guard()->check()) {
                    $widget->updated_by = Auth::user()['id'];
                }
                $widget->save();
    
                return $this->success($widget, __('global.alert.update_success', [
                    'attribute' => __('module/widget.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/widget.caption')
                ]));
            }
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Widget
     * @param array $where
     */
    public function trashWidget($where)
    {
        $widget = $this->getWidget($where);

        try {

            if ($widget['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $widget['created_by']) {
                        return $this->error($widget,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/widget.caption')
                        ]));
                    }

                    $widget->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $widget->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/widget.caption')
                ]));
    
            } else {
                return $this->error($widget,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/widget.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Widget
     * @param array $where
     */
    public function restoreWidget($where)
    {
        $widget = $this->widgtModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            
            $widget->restore();

            return $this->success($widget, __('global.alert.restore_success', [
                'attribute' => __('module/widget.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Widget (Permanent)
     * @param array $where
     */
    public function deleteWidget($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $widget = $this->widgtModel->onlyTrashed()->firstWhere($where);
        } else {
            $widget = $this->getWidget($where);
        }

        try {
            
            $path = resource_path('views/frontend/widget/'.$widget['template'].'.blade.php');
            File::delete($path);

            $widget->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/widget.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}