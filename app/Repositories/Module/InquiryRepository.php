<?php

namespace App\Repositories\Module;

use App\Models\Module\Inquiry\Inquiry;
use App\Models\Module\Inquiry\InquiryField;
use App\Models\Module\Inquiry\InquiryForm;
use App\Repositories\Feature\LanguageRepository;
use App\Repositories\IndexUrlRepository;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class InquiryRepository
{
    use ApiResponser;

    private $inquiryModel, $inquiryFieldModel, $inquiryFormModel, $language, $indexUrl;

    public function __construct(
        Inquiry $inquiryModel,
        InquiryField $inquiryFieldModel,
        InquiryForm $inquiryFormModel,
        LanguageRepository $language,
        IndexUrlRepository $indexUrl
    )
    {
        $this->inquiryModel = $inquiryModel;
        $this->inquiryFieldModel = $inquiryFieldModel;
        $this->inquiryFormModel = $inquiryFormModel;
        $this->language = $language;
        $this->indexUrl = $indexUrl;
    }

    //--------------------------------------------------------------------------
    // INQUIRY
    //--------------------------------------------------------------------------

    /**
     * Get Inquiry List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getInquiryList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $inquiry = $this->inquiryModel->query();

        if ($isTrash == true)
            $inquiry->onlyTrashed();

        if (isset($filter['publish']))
            $inquiry->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $inquiry->where('public', $filter['public']);

        if (isset($filter['approved']))
            $inquiry->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $inquiry->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $inquiry->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $inquiry->when($filter['q'], function ($inquiry, $q) {
                $inquiry->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(body, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $inquiry->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $inquiry->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $inquiry->paginate($limit);
        } else {

            if ($limit > 0)
                $inquiry->limit($limit);

            $result = $inquiry->get();
        }

        return $result;
    }

    /**
     * Get Inquiry One
     * @param array $where
     * @param array $with
     */
    public function getInquiry($where, $with = [])
    {
        $inquiry = $this->inquiryModel->query();

        if (!empty($with))
            $inquiry->with($with);

        $result = $inquiry->firstWhere($where);;

        return $result;
    }

    /**
     * Create Inquiry
     * @param array $data
     */
    public function storeInquiry($data)
    {
        try {

            DB::beginTransaction();

            $inquiry = new Inquiry;
            $this->setFieldInquiry($data, $inquiry);
            $inquiry->position = $this->inquiryModel->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.inquiry.approval') == true) {
                    $inquiry->approved = 2;
                }
                $inquiry->created_by = Auth::user()['id'];

            $inquiry->save();

            try {

                DB::commit();
                $slug = Str::slug(strip_tags($data['slug']), '-');
                $data['slug'] = $slug;
                $data['module'] = 'inquiry';
                $this->indexUrl->storeAssociate($data, $inquiry);

                $setPath = 'views/frontend/inquiries/'.$slug.'.blade.php';
                if (!file_exists(resource_path($setPath))) {
                    File::copy(resource_path('views/frontend/inquiries/detail.blade.php'),
                        resource_path($setPath));
                }

                return $this->success($inquiry,  __('global.alert.create_success', [
                    'attribute' => __('module/inquiry.caption')
                ]));

            } catch (Exception $e) {

                return $this->error(null,  $e->getMessage());
            }

            return $this->success($inquiry,  __('global.alert.create_success', [
                'attribute' => __('module/inquiry.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Inquiry
     * @param array $data
     * @param array $where
     */
    public function updateInquiry($data, $where)
    {
        $inquiry = $this->getInquiry($where);
        $oldSlug = $inquiry['slug'];

        try {

            $this->setFieldInquiry($data, $inquiry);
            if (Auth::guard()->check())
                $inquiry->updated_by = Auth::user()['id'];

            $inquiry->save();

            $slug = Str::slug(strip_tags($data['slug']), '-');
            $this->indexUrl->updateAssociate($slug, ['id' => $inquiry['indexing']['id']]);

            if ($oldSlug != $slug) {

                File::copy(resource_path('views/frontend/inquiries/'.$oldSlug.'.blade.php'),
                    resource_path('views/frontend/inquiries/'.$slug.'.blade.php'));

                $path = resource_path('views/frontend/inquiries/'.$oldSlug.'.blade.php');
                File::delete($path);
            }

            return $this->success($inquiry,  __('global.alert.update_success', [
                'attribute' => __('module/inquiry.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Inquiry
     * @param array $data
     * @param model $inquiry
     */
    private function setFieldInquiry($data, $inquiry)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];

            $body[$value['iso_codes']] = ($data['body_'.$value['iso_codes']] == null) ?
                $data['body_'.$langDefault] : $data['body_'.$value['iso_codes']];

            $afterBody[$value['iso_codes']] = ($data['after_body_'.$value['iso_codes']] == null) ?
                $data['after_body_'.$langDefault] : $data['after_body_'.$value['iso_codes']];
        }

        $inquiry->slug = Str::slug(strip_tags($data['slug']), '-');
        $inquiry->name = $name;
        $inquiry->body = $body;
        $inquiry->after_body = $afterBody;
        $inquiry->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $inquiry->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];
        if (!empty($data['email'])) {
            $inquiry->email = explode(',', $data['email']);
        } else {
            $inquiry->email = null;
        }
        $inquiry->longitude = $data['longitude'] ?? null;
        $inquiry->latitude = $data['latitude'] ?? null;

        $inquiry->publish = (bool)$data['publish'];
        $inquiry->public = (bool)$data['public'];
        $inquiry->locked = (bool)$data['locked'];
        $inquiry->detail = (bool)$data['detail'];
        $inquiry->mail_sender_template = $data['mail_sender_template'];
        $inquiry->content_template = isset($data['content_template']) ? $data['content_template'] : null;
        $inquiry->config = [
            'show_body' => (bool)$data['config_show_body'],
            'show_after_body' => (bool)$data['config_show_after_body'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_map' => (bool)$data['config_show_map'],
            'show_form' => (bool)$data['config_show_form'],
            'lock_form' => (bool)$data['config_lock_form'],
            'send_mail_sender' => (bool)$data['config_send_mail_sender'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];
        $inquiry->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ];

        if (isset($data['cf_name'])) {

            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $inquiry->custom_fields = $customField;
        } else {
            $inquiry->custom_fields = null;
        }

        return $inquiry;
    }

    /**
     * Status Inquiry (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusInquiry($field, $where)
    {
        $inquiry = $this->getInquiry($where);

        try {

            $value = !$inquiry[$field];
            if ($field == 'approved') {
                $value = $inquiry['approved'] == 1 ? 0 : 1;
            }

            $inquiry->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $inquiry['updated_by'],
            ]);

            if ($field == 'publish') {
                $inquiry->menus()->update([
                    'publish' => $inquiry['publish']
                ]);
                $inquiry->widgets()->update([
                    'publish' => $inquiry['publish']
                ]);
            }

            return $this->success($inquiry, __('global.alert.update_success', [
                'attribute' => __('module/inquiry.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Inquiry
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortInquiry($where, $position)
    {
        $inquiry = $this->getInquiry($where);

        $inquiry->position = $position;
        if (Auth::guard()->check()) {
            $inquiry->updated_by = Auth::user()['id'];
        }
        $inquiry->save();

        return $inquiry;
    }

    /**
     * Set Position Inquiry
     * @param array $where
     * @param int $position
     */
    public function positionInquiry($where, $position)
    {
        $inquiry = $this->getInquiry($where);

        try {

            if ($position >= 1) {

                $this->inquiryModel->where('position', $position)->update([
                    'position' => $inquiry['position'],
                ]);

                $inquiry->position = $position;
                if (Auth::guard()->check()) {
                    $inquiry->updated_by = Auth::user()['id'];
                }
                $inquiry->save();

                return $this->success($inquiry, __('global.alert.update_success', [
                    'attribute' => __('module/inquiry.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/event.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Record Hits
     * @param array $where
     */
    public function recordHits($where)
    {
        $inquiry = $this->getInquiry($where);

        if (empty(Session::get('inquiryHits-'.$inquiry['id']))) {
            Session::put('inquiryHits-'.$inquiry['id'], $inquiry['id']);
            $inquiry->hits = ($inquiry->hits+1);
            $inquiry->timestamps = false;
            $inquiry->save();
        }

        return $inquiry;
    }

    /**
     * Trash Inquiry
     * @param array $where
     */
    public function trashInquiry($where)
    {
        $inquiry = $this->getInquiry($where);

        try {

            $fields = $inquiry->fields()->count();
            $forms = $inquiry->forms()->count();

            if ($inquiry['locked'] == 0 && $fields == 0 && $forms == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $inquiry['created_by']) {
                        return $this->error($inquiry,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/inquiry.caption')
                        ]));
                    }

                    $inquiry->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $inquiry->menus()->delete();
                $inquiry->widgets()->delete();
                // $inquiry->indexing->delete();
                $inquiry->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/inquiry.caption')
                ]));

            } else {
                return $this->error($inquiry,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/inquiry.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Inquiry
     * @param array $where
     */
    public function restoreInqury($where)
    {
        $inquiry = $this->inquiryModel->onlyTrashed()->firstWhere($where);

        try {

            $checkSlug = $this->getInquiry(['slug' => $inquiry['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/inquiry.caption')
                ]));
            }

            //restore data yang bersangkutan
            $inquiry->menus()->restore();
            $inquiry->widgets()->restore();
            // $inquiry->indexing()->restore();
            $inquiry->restore();

            return $this->success($inquiry, __('global.alert.restore_success', [
                'attribute' => __('module/inquiry.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Inquiry (Permanent)
     * @param array $where
     */
    public function deleteInquiry($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $inquiry = $this->inquiryModel->onlyTrashed()->firstWhere($where);
        } else {
            $inquiry = $this->getInquiry($where);
        }

        try {

            $path = resource_path('views/frontend/inquiries/'.$inquiry['slug'].'.blade.php');
                File::delete($path);

            $inquiry->menus()->forceDelete();
            $inquiry->widgets()->forceDelete();
            $inquiry->indexing()->forceDelete();
            $inquiry->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/inquiry.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // INQUIRY FIELD
    //--------------------------------------------------------------------------

    /**
     * Get Field List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getFieldList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $field = $this->inquiryFieldModel->query();

        if ($isTrash == true)
            $field->onlyTrashed();

        if (isset($filter['inquiry_id']))
            $field->where('inquiry_id', $filter['inquiry_id']);

        if (isset($filter['publish']))
            $field->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $field->where('public', $filter['public']);

        if (isset($filter['approved']))
            $field->where('approved', $filter['approved']);

        if (isset($filter['created_by']))
            $field->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $field->when($filter['q'], function ($field, $q) {
                $field->whereRaw('LOWER(JSON_EXTRACT(label, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhere('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $field->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $field->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $field->paginate($limit);
        } else {

            if ($limit > 0)
                $field->limit($limit);

            $result = $field->get();
        }

        return $result;
    }

    /**
     * Get Field One
     * @param array $where
     * @param array $with
     */
    public function getField($where, $with = [])
    {
        $field = $this->inquiryFieldModel->query();

        if (!empty($with))
            $field->with($with);

        $result = $field->firstWhere($where);;

        return $result;
    }

    /**
     * Create Field
     * @param array $data
     */
    public function storeField($data)
    {
        try {

            $checkName = $this->inquiryFieldModel->firstWhere([
                'inquiry_id' => $data['inquiry_id'],
                'name' => $data['name']
            ]);
            if (!empty($checkName)) {
                return $this->error(null,   __('global.alert.exists', [
                    'attribute' => __('module/inquiry.field.label.field2')
                ]));
            }

            $field = new InquiryField;
            $field->inquiry_id = $data['inquiry_id'];
            $this->setField($data, $field);
            $field->position = $this->inquiryFieldModel->where('inquiry_id', $data['inquiry_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.inquiry.field.approval') == true) {
                    $field->approved = 2;
                }
                $field->created_by = Auth::user()['id'];

            $field->save();

            return $this->success($field,  __('global.alert.create_success', [
                'attribute' => __('module/inquiry.field.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Field
     * @param array $data
     * @param array $where
     */
    public function updateField($data, $where)
    {
        $field = $this->getField($where);

        try {


            $checkName = $this->inquiryFieldModel->firstWhere([
                'inquiry_id' => $field['inquiry_id'],
                'name' => $data['name']
            ]);

            if ($field['name'] != $data['name'] && !empty($checkName)) {
                return $this->error(null,   __('global.alert.exists', [
                    'attribute' => __('module/inquiry.field.label.field2')
                ]));
            }

            $this->setField($data, $field);
            if (Auth::guard()->check())
                $field->updated_by = Auth::user()['id'];

            $field->save();

            return $this->success($field,  __('global.alert.update_success', [
                'attribute' => __('module/inquiry.field.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field
     * @param array $data
     * @param model $field
     */
    private function setField($data, $field)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('app.fallback_locale');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $label[$value['iso_codes']] = ($data['label_'.$value['iso_codes']] == null) ?
                $data['label_'.$langDefault] : $data['label_'.$value['iso_codes']];

            $placeholder[$value['iso_codes']] = ($data['placeholder_'.$value['iso_codes']] == null) ?
                $data['placeholder_'.$langDefault] : $data['placeholder_'.$value['iso_codes']];
        }

        $field->name = Str::slug($data['name'], '_');
        $field->type = $data['type'];
        $field->label = $label;
        $field->placeholder = $placeholder;
        $field->properties = [
            'type' => $data['property_type'] ?? null,
            'class' => $data['property_class'] ?? null,
            'attribute' => $data['property_attribute'] ?? null,
        ];
        if (isset($data['validation'])) {
            $field->validation = $data['validation'];
        }
        $field->is_unique = (bool)$data['is_unique'];
        $field->publish = (bool)$data['publish'];
        $field->public = (bool)$data['public'];
        $field->locked = (bool)$data['locked'];

        if (isset($data['opt_label'])) {

            $options = [];
            foreach ($data['opt_label'] as $key => $value) {
                $options[$value] = $data['opt_value'][$key];
            }

            $field->options = $options;
        } else {
            $field->options = null;
        }

        return $field;
    }

    /**
     * Status Field (boolean type only)
     * @param string $fieldd
     * @param array $where
     */
    public function statusField($fieldd, $where)
    {
        $field = $this->getField($where);

        try {

            $value = !$field[$fieldd];
            if ($fieldd == 'approved') {
                $value = $field['approved'] == 1 ? 0 : 1;
            }

            $field->update([
                $fieldd => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $field['updated_by'],
            ]);

            return $this->success($field, __('global.alert.update_success', [
                'attribute' => __('module/inquiry.field.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Field
     * @param array $where
     * @param int $position
     */
    public function sortField($where, $position)
    {
        $field = $this->getField($where);

        $field->position = $position;
        if (Auth::guard()->check()) {
            $field->updated_by = Auth::user()['id'];
        }
        $field->save();

        return $field;
    }

    /**
     * Set Position Field
     * @param array $where
     * @param int $position
     */
    public function positionField($where, $position)
    {
        $field = $this->getField($where);

        try {

            if ($position >= 1) {

                $this->inquiryFieldModel->where('inquiry_id', $field['inquiry_id'])
                    ->where('position', $position)->update([
                    'position' => $field['position'],
                ]);

                $field->position = $position;
                if (Auth::guard()->check()) {
                    $field->updated_by = Auth::user()['id'];
                }
                $field->save();

                return $this->success($field, __('global.alert.update_success', [
                    'attribute' => __('module/inquiry.field.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/inquiry.field.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Field
     * @param array $where
     */
    public function trashField($where)
    {
        $field = $this->getField($where);

        try {

            if ($field['locked'] == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $field['created_by']) {
                        return $this->error($field,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/inquiry.field.caption')
                        ]));
                    }

                    $field->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $field->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/inquiry.field.caption')
                ]));

            } else {

                return $this->error($field,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/inquiry.field.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Field
     * @param array $where
     */
    public function restoreField($where)
    {
        $field = $this->inquiryFieldModel->onlyTrashed()->firstWhere($where);

        try {

            //restore data yang bersangkutan
            $field->restore();

            return $this->success($field, __('global.alert.restore_success', [
                'attribute' => __('module/inquiry.field.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Field (Permanent)
     * @param array $where
     */
    public function deleteField($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $field = $this->inquiryFieldModel->onlyTrashed()->firstWhere($where);

        } else {
            $field = $this->getField($where);
        }

        try {

            $field->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/inquiry.field.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // INQUIRY FORM
    //--------------------------------------------------------------------------

    /**
     * Get Form List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getFormList($filter = [], $withPaginate = true, $limit = 10,
        $isTrash = false, $with = [], $orderBy = [])
    {
        $form = $this->inquiryFormModel->query();

        if ($isTrash == true)
            $form->onlyTrashed();

        if (isset($filter['inquiry_id']))
            $form->where('inquiry_id', $filter['inquiry_id']);

        if (isset($filter['status']))
            $form->where('status', $filter['status']);

        if (isset($filter['exported']))
            $form->where('exported', $filter['exported']);

        if (isset($filter['q']))
            $form->when($filter['q'], function ($form, $q) {
                $form->where('fields', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $form->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $form->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $form->paginate($limit);
        } else {

            if ($limit > 0)
                $form->limit($limit);

            $result = $form->get();
        }

        return $result;
    }

    /**
     * Get Form One
     * @param array $where
     * @param array $with
     */
    public function getForm($where, $with = [])
    {
        $form = $this->inquiryFormModel->query();

        if (!empty($with))
            $form->with($with);

        $result = $form->firstWhere($where);;

        return $result;
    }

    /**
     * Record Form
     * @param array $data
     */
    public function recordForm($data)
    {
        try {

            $inquiry = $this->getInquiry(['id' => $data['inquiry_id']]);
            $getFields = $this->getFieldList([
                'inquiry_id' => $data['inquiry_id'],
                'publish' => 1,
                'approved' => 1
            ], false, 0);

            foreach ($getFields as $key => $value) {
                $fields[$value['name']] = strip_tags($data[$value['name']]) ?? null;
            }

            if ($inquiry['config']['show_form'] == true) {

                $form = new InquiryForm;
                $form->inquiry_id = $data['inquiry_id'];
                $form->ip_address = request()->ip();
                $form->fields = $fields;
                $form->submit_time = now();
                $form->save();

                return $this->success($form, __('global.alert.create_success', [
                    'attribute' => __('module/inquiry.form.caption')
                ]));

            } else {
                return $this->success($inquiry, __('global.alert.create_failed', [
                    'attribute' => __('module/inquiry.form.caption')
                ]));
            }

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }

    }

    /**
     * Status Form (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusForm($field, $where)
    {
        $form = $this->getForm($where);

        try {

            $form->update([
                $field => !$form[$field]
            ]);

            return $this->success($form, __('global.alert.update_success', [
                'attribute' => __('module/inquiry.form.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Form (Permanent)
     * @param array $where
     */
    public function deleteForm($where)
    {
        $form = $this->getForm($where);

        try {

            $form->delete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/inquiry.form.caption')
            ]));

        } catch (Exception $e) {

            return $this->error(null, $e->getMessage());
        }
    }
}
