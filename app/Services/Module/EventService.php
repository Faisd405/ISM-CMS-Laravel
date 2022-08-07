<?php

namespace App\Services\Module;

use App\Models\Module\Event\Event;
use App\Models\Module\Event\EventField;
use App\Models\Module\Event\EventForm;
use App\Services\Feature\LanguageService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EventService
{
    use ApiResponser;

    private $eventModel, $eventFieldModel, $eventFormModel, $language;

    public function __construct(
        Event $eventModel,
        EventField $eventFieldModel,
        EventForm $eventFormModel,
        LanguageService $language
    )
    {
        $this->eventModel = $eventModel;
        $this->eventFieldModel = $eventFieldModel;
        $this->eventFormModel = $eventFormModel;
        $this->language = $language;
    }

    //--------------------------------------------------------------------------
    // EVENT
    //--------------------------------------------------------------------------

    /**
     * Get Event List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getEventList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $event = $this->eventModel->query();

        if ($isTrash == true)
            $event->onlyTrashed();

        if (isset($filter['type']))
            $event->where('type', $filter['type']);

        if (isset($filter['publish']))
            $event->where('publish', $filter['publish']);

        if (isset($filter['public']))
            $event->where('public', $filter['public']);

        if (isset($filter['approved']))
            $event->where('approved', $filter['approved']);

        if (isset($filter['detail']))
            $event->where('detail', $filter['detail']);

        if (isset($filter['created_by']))
            $event->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $event->when($filter['q'], function ($event, $q) {
                $event->whereRaw('LOWER(JSON_EXTRACT(name, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(description, "$.'.App::getLocale().'")) like ?', ['"%' . strtolower($q) . '%"']);
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $event->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $event->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $event->paginate($limit);
        } else {

            if ($limit > 0)
                $event->limit($limit);

            $result = $event->get();
        }
        
        return $result;
    }

    /**
     * Get Event One
     * @param array $where
     * @param array $with
     */
    public function getEvent($where, $with = [])
    {
        $event = $this->eventModel->query();
        
        if (!empty($with))
            $event->with($with);
        
        $result = $event->firstWhere($where);;

        return $result;
    }

    /**
     * Create Event
     * @param array $data
     */
    public function storeEvent($data)
    {
        try {

            $event = new Event;
            $this->setFieldEvent($data, $event);
            $event->position = $this->eventModel->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.event.approval') == true) {
                    $event->approved = 2;
                }
                $event->created_by = Auth::user()['id'];

            $event->save();

            $slug = Str::slug(strip_tags($data['slug']), '-');

            $setPath = 'views/frontend/events/'.$slug.'.blade.php';
            if (!file_exists(resource_path($setPath))) {
                File::copy(resource_path('views/frontend/events/detail.blade.php'), 
                    resource_path($setPath));
            }

            return $this->success($event,  __('global.alert.create_success', [
                'attribute' => __('module/event.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Event
     * @param array $data
     * @param array $where
     */
    public function updateEvent($data, $where)
    {
        $event = $this->getEvent($where);
        $oldSlug = $event['slug'];

        try {
            
            $this->setFieldEvent($data, $event);
            if (Auth::guard()->check())
                $event->updated_by = Auth::user()['id'];

            $event->save();

            $slug = Str::slug(strip_tags($data['slug']), '-');

            if ($oldSlug != $slug) {
                
                File::copy(resource_path('views/frontend/events/'.$oldSlug.'.blade.php'), 
                    resource_path('views/frontend/events/'.$slug.'.blade.php'));

                $path = resource_path('views/frontend/events/'.$oldSlug.'.blade.php');
                File::delete($path);
            }

            return $this->success($event,  __('global.alert.update_success', [
                'attribute' => __('module/event.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Field Event
     * @param array $data
     * @param model $event
     */
    private function setFieldEvent($data, $event)
    {
        $multiple = config('cms.module.feature.language.multiple');
        $langDefault = config('cms.module.feature.language.default');
        $languages = $this->language->getLanguageActive($multiple);
        foreach ($languages as $key => $value) {
            $name[$value['iso_codes']] = ($data['name_'.$value['iso_codes']] == null) ?
                $data['name_'.$langDefault] : $data['name_'.$value['iso_codes']];

            $description[$value['iso_codes']] = ($data['description_'.$value['iso_codes']] == null) ?
                $data['description_'.$langDefault] : $data['description_'.$value['iso_codes']];

            $formDescription[$value['iso_codes']] = ($data['form_description_'.$value['iso_codes']] == null) ?
                $data['form_description_'.$langDefault] : $data['form_description_'.$value['iso_codes']];
        }

        $event->slug = Str::slug(strip_tags($data['slug']), '-');
        $event->name = $name;
        $event->description = $description;
        $event->form_description = $formDescription;
        $event->register_code = $data['register_code'] ?? null;
        $event->type = $data['type'];
        $event->place = $data['place'] ?? null;
        $event->links = [
            'meeting_url' => $data['meeting_url'] ?? null,
            'meeting_id' => $data['meeting_id'] ?? null,
            'meeting_passcode' => $data['meeting_passcode'] ?? null,
        ];
        $event->start_date = $data['start_date'] ?? null;
        $event->end_date = $data['end_date'] ?? null;
        if (!empty($data['email'])) {
            $event->email = explode(',', $data['email']);
        } else {
            $event->email = null;
        }
        $event->cover = [
            'filepath' => Str::replace(url('/storage'), '', $data['cover_file']) ?? null,
            'title' => $data['cover_title'] ?? null,
            'alt' => $data['cover_alt'] ?? null,
        ];
        $event->banner = [
            'filepath' => Str::replace(url('/storage'), '', $data['banner_file']) ?? null,
            'title' => $data['banner_title'] ?? null,
            'alt' => $data['banner_alt'] ?? null,
        ];

        $event->publish = (bool)$data['publish'];
        $event->public = (bool)$data['public'];
        $event->locked = (bool)$data['locked'];
        $event->detail = (bool)$data['detail'];
        $event->content_template = isset($data['content_template']) ? $data['content_template'] : null;
        $event->config = [
            'show_description' => (bool)$data['config_show_description'],
            'show_form_description' => (bool)$data['config_show_form_description'],
            'show_register_code' => (bool)$data['config_show_register_code'],
            'show_cover' => (bool)$data['config_show_cover'],
            'show_banner' => (bool)$data['config_show_banner'],
            'show_form' => (bool)$data['config_show_form'],
            'lock_form' => (bool)$data['config_lock_form'],
            'show_custom_field' => (bool)$data['config_show_custom_field'],
        ];
        $event->seo = [
            'title' => $data['meta_title'] ?? null,
            'description' => $data['meta_description'] ?? null,
            'keywords' => $data['meta_keywords'] ?? null,
        ];

        if (isset($data['cf_name'])) {
            
            $customField = [];
            foreach ($data['cf_name'] as $key => $value) {
                $customField[$value] = $data['cf_value'][$key];
            }

            $event->custom_fields = $customField;
        } else {
            $event->custom_fields = null;
        }

        return $event;
    }

    /**
     * Status Event (boolean type only)
     * @param string $field
     * @param array $where
     */
    public function statusEvent($field, $where)
    {
        $event = $this->getEvent($where);

        try {
            
            $value = !$event[$field];
            if ($field == 'approved') {
                $value = $event['approved'] == 1 ? 0 : 1;
            }
            
            $event->update([
                $field => $value,
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $event['updated_by'],
            ]);

            if ($field == 'publish') {
                $event->menus()->update([
                    'publish' => $event['publish']
                ]);
                $event->widgets()->update([
                    'publish' => $event['publish']
                ]);
            }

            return $this->success($event, __('global.alert.update_success', [
                'attribute' => __('module/event.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Sort Event
     * @param array $where
     * @param int $position
     * @param int $parent
     */
    public function sortEvent($where, $position)
    {
        $event = $this->getEvent($where);

        $event->position = $position;
        if (Auth::guard()->check()) {
            $event->updated_by = Auth::user()['id'];
        }
        $event->save();

        return $event;
    }

    /**
     * Set Position Event
     * @param array $where
     * @param int $position
     */
    public function positionEvent($where, $position)
    {
        $event = $this->getEvent($where);
        
        try {

            if ($position >= 1) {
    
                $this->eventModel->where('position', $position)->update([
                    'position' => $event['position'],
                ]);
    
                $event->position = $position;
                if (Auth::guard()->check()) {
                    $event->updated_by = Auth::user()['id'];
                }
                $event->save();
    
                return $this->success($event, __('global.alert.update_success', [
                    'attribute' => __('module/event.caption')
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
        $event = $this->getEvent($where);
        
        if (empty(Session::get('eventHits-'.$event['id']))) {
            Session::put('eventHits-'.$event['id'], $event['id']);
            $event->hits = ($event->hits+1);
            $event->timestamps = false;
            $event->save();
        }

        return $event;
    }

    /**
     * Trash Event
     * @param array $where
     */
    public function trashEvent($where)
    {
        $event = $this->getEvent($where);

        try {
            
            $fields = $event->fields()->count();
            $forms = $event->forms()->count();

            if ($event['locked'] == 0 && $fields == 0 && $forms == 0) {

                if (Auth::guard()->check()) {

                    if (Auth::user()->hasRole('editor') && Auth::user()['id'] != $event['created_by']) {
                        return $this->error($event,  __('global.alert.delete_failed_used', [
                            'attribute' => __('module/event.caption')
                        ]));
                    }

                    $event->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $event->menus()->delete();
                $event->widgets()->delete();
                $event->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/event.caption')
                ]));
    
            } else {
                return $this->error($event,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/event.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Event
     * @param array $where
     */
    public function restoreEvent($where)
    {
        $event = $this->eventModel->onlyTrashed()->firstWhere($where);

        try {
            
            $checkSlug = $this->getEvent(['slug' => $event['slug']]);
            if (!empty($checkSlug)) {
                return $this->error(null, __('global.alert.restore_failed', [
                    'attribute' => __('module/event.caption')
                ]));
            }
            
            //restore data yang bersangkutan
            $event->menus()->restore();
            $event->widgets()->restore();
            $event->restore();

            return $this->success($event, __('global.alert.restore_success', [
                'attribute' => __('module/event.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Event (Permanent)
     * @param array $where
     */
    public function deleteEvent($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $event = $this->eventModel->onlyTrashed()->firstWhere($where);
        } else {
            $event = $this->getEvent($where);
        }

        try {

            $path = resource_path('views/frontend/events/'.$event['slug'].'.blade.php');
                File::delete($path);
                
            $event->menus()->forceDelete();
            $event->widgets()->forceDelete();
            $event->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/event.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // EVENT FIELD
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
        $field = $this->eventFieldModel->query();

        if ($isTrash == true)
            $field->onlyTrashed();

        if (isset($filter['event_id']))
            $field->where('event_id', $filter['event_id']);

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
        $field = $this->eventFieldModel->query();
        
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

            $checkName = $this->eventFieldModel->firstWhere([
                'event_id' => $data['event_id'],
                'name' => $data['name']
            ]);
            if (!empty($checkName)) {
                return $this->error(null,   __('global.alert.exists', [
                    'attribute' => __('module/inquiry.field.label.field2')
                ]));
            }

            $field = new EventField;
            $field->event_id = $data['event_id'];
            $this->setField($data, $field);
            $field->position = $this->eventFieldModel->where('event_id', $data['event_id'])->max('position') + 1;

            if (Auth::guard()->check())
                if (!Auth::user()->hasRole('developer|super') && config('module.event.field.approval') == true) {
                    $field->approved = 2;
                }
                $field->created_by = Auth::user()['id'];

            $field->save();

            return $this->success($field,  __('global.alert.create_success', [
                'attribute' => __('module/event.field.caption')
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
            
            $checkName = $this->eventFieldModel->firstWhere([
                'event_id' => $field['event_id'],
                'name' => $data['name']
            ]);

            if ($field['name'] != $data['name'] && !empty($checkName)) {
                return $this->error(null,   __('global.alert.exists', [
                    'attribute' => __('module/event.field.label.field2')
                ]));
            }

            $this->setField($data, $field);
            if (Auth::guard()->check())
                $field->updated_by = Auth::user()['id'];

            $field->save();

            return $this->success($field,  __('global.alert.update_success', [
                'attribute' => __('module/event.field.caption')
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
        $langDefault = config('cms.module.feature.language.default');
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
        $field->options = $data['options'] ?? null;
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
                'attribute' => __('module/event.field.caption')
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
    
                $this->eventFieldModel->where('event_id', $field['event_id'])
                    ->where('position', $position)->update([
                    'position' => $field['position'],
                ]);
    
                $field->position = $position;
                if (Auth::guard()->check()) {
                    $field->updated_by = Auth::user()['id'];
                }
                $field->save();
    
                return $this->success($field, __('global.alert.update_success', [
                    'attribute' => __('module/event.field.caption')
                ]));

            } else {

                return $this->error(null, __('global.alert.update_failed', [
                    'attribute' => __('module/event.field.caption')
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
                            'attribute' => __('module/event.field.caption')
                        ]));
                    }

                    $field->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $field->delete();

                return $this->success(null,  __('global.alert.delete_success', [
                    'attribute' => __('module/event.field.caption')
                ]));

            } else {

                return $this->error($field,  __('global.alert.delete_failed_used', [
                    'attribute' => __('module/event.field.caption')
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
        $field = $this->eventFieldModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $field->restore();

            return $this->success($field, __('global.alert.restore_success', [
                'attribute' => __('module/event.field.caption')
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
            $field = $this->eventFieldModel->onlyTrashed()->firstWhere($where);

        } else {
            $field = $this->getField($where);
        }

        try {

            $field->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('module/event.field.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    //--------------------------------------------------------------------------
    // EVENT FORM
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
        $form = $this->eventFormModel->query();

        if ($isTrash == true)
            $form->onlyTrashed();

        if (isset($filter['event_id']))
            $form->where('event_id', $filter['event_id']);

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
        $form = $this->eventFormModel->query();
        
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
            
            $event = $this->getEvent(['id' => $data['event_id']]);
            $getFields = $this->getFieldList([
                'event_id' => $data['event_id'],
                'publish' => 1,
                'approved' => 1
            ], false, 0);

            foreach ($getFields as $key => $value) {
                $fields[$value['name']] = strip_tags($data[$value['name']]) ?? null;
            }

            $registerNumber = $this->eventFormModel->where('event_id', $data['event_id'])->max('register_code') + 1;

            if ($event['config']['show_form'] == true) {

                $form = new eventForm;
                $form->event_id = $data['event_id'];
                $form->register_code = sprintf("%03d", $registerNumber);
                $form->ip_address = request()->ip();
                $form->fields = $fields;
                $form->submit_time = now();
                $form->save();

                return $this->success($form, __('global.alert.create_success', [
                    'attribute' => __('module/event.form.caption')
                ]));
            
            } else {
                return $this->success($event, __('global.alert.create_failed', [
                    'attribute' => __('module/event.form.caption')
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
                'attribute' => __('module/event.form.caption')
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
                'attribute' => __('module/event.form.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}