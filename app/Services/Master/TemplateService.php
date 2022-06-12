<?php

namespace App\Services\Master;

use App\Models\Master\Template;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TemplateService
{
    use ApiResponser;

    private $templateModel;

    public function __construct(
        Template $templateModel
    )
    {
        $this->templateModel = $templateModel;
    }

    /**
     * Get Template List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getTemplateList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $template = $this->templateModel->query();

        if ($isTrash == true)
            $template->onlyTrashed();

        if (isset($filter['module']))
            $template->where('module', $filter['module']);

        if (isset($filter['type']))
            $template->where('type', $filter['type']);

        if (isset($filter['created_by']))
            $template->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $template->when($filter['q'], function ($template, $q) {
                $template->where('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $template->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $template->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $template->paginate($limit);
        } else {
            $result = $template->get();
        }

        return $result;
    }

    /**
     * Get Teemplate One
     * @param array $where
     * @param array $with
     */
    public function getTemplate($where, $with = [])
    {
        $template = $this->templateModel->query();

        if (!empty($with))
            $template->with($with);

        $result = $template->firstWhere($where);

        return $result;
    }

    /**
     * Create Template
     * @param array $data
     */
    public function store($data)
    {
        try {

            if ($data['module'] == 'content_section' || $data['module'] == 'gallery_category') {
                $type = ($data['type'] == 0) ? 1 : $data['type'];
            } else {
                $type = ($data['type'] > 0) ? 0 : $data['type'];
            }

            $templateType = config('cms.module.master.template.type.'.$type);
            $templateResource = config('cms.module.master.template.mod.'.$data['module']);
            $fileName = Str::slug($data['filename'], '-').'.blade.php';
            $filePath = $templateResource['full'].$templateResource[$templateType].'/';
            $setPath = $filePath.$fileName;

            if (!file_exists(resource_path($setPath))) {
                
                if ($type == 0) {
                    File::copy(resource_path($templateResource['full'].'/detail.blade.php'), 
                        resource_path($templateResource['full'].'/custom/'.$fileName));
                } else {

                    if ($type == 1) {
                        $tempType = 'list.blade.php';
                    } else {
                        $tempType = 'detail.blade.php';
                    }
                    
                    File::copy(resource_path($templateResource['full'].'/'.$tempType), 
                        resource_path($setPath));
                    // $path = resource_path($setPath);
                    // File::put($path, '');
                }
                
            } else {
                return $this->error(null,  __('master/template.alert.file_exist'));
            }

            $template = $this->templateModel->create([
                'name' => $data['name'],
                'module' => $data['module'],
                'type' => $type,
                'filepath' => $filePath,
                'filename' => $fileName,
                'content_template' => isset($data['content_template']) ? $data['content_template'] : null,
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($template,  __('global.alert.create_success', [
                'attribute' => __('master/template.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Template
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $template = $this->getTemplate($where);

        try {
            
            $template->update([
                'name' => $data['name'],
                'content_template' => isset($data['content_template']) ? $data['content_template'] : null,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $template['updated_by'],
            ]);

            return $this->success($template,  __('global.alert.update_success', [
                'attribute' => __('master/template.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Trash Template
     * @param array $where
     */
    public function trash($where)
    {
        $template = $this->getTemplate($where);

        try {

            $pages = $template->pages()->count();
            $contentSectionLists = $template->contentSectionLists()->count();
            $contentSectionDetails = $template->contentSectionDetails()->count();
            $contentCategories = $template->contentCategories()->count();
            $contentPosts = $template->contentPosts()->count();
            $galleryCategoryLists = $template->galleryCategoryLists()->count();
            $galleryCategoryDetails = $template->galleryCategoryDetails()->count();
            $galleryAlbums = $template->galleryAlbums()->count();
            $documentCategories = $template->documentCategories()->count();
            $linkCategories = $template->linkCategories()->count();
            
            if ($pages == 0 || $contentSectionLists == 0 && $contentSectionDetails == 0
                || $contentCategories == 0 || $contentPosts == 0 || $galleryCategoryLists == 0
                || $galleryCategoryDetails == 0 || $galleryAlbums == 0 || $documentCategories == 0
                || $linkCategories == 0) {
        
                if (Auth::guard()->check()) {
                    $template->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $template->delete();

                return $this->success($template,  __('global.alert.delete_success', [
                    'attribute' => __('master/template.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('master/template.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Template
     * @param array $where
     */
    public function restore($where)
    {
        $template = $this->templateModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $template->restore();

            return $this->success($template, __('global.alert.restore_success', [
                'attribute' => __('master/template.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Template (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $template = $this->templateModel->onlyTrashed()->firstWhere($where);
        } else {
            $template = $this->getTemplate($where);
        }

        try {
            
            $path = resource_path($template['filepath'].$template['filename']);
                File::delete($path);
                
            $template->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('master/template.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}