<?php

namespace App\Services\Feature;

use App\Models\Feature\Language;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class LanguageService
{
    use ApiResponser;

    private $languageModel;

    public function __construct(
        Language $languageModel
    )
    {
        $this->languageModel = $languageModel;
    }

    /**
     * Get Language List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getLanguageList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $language = $this->languageModel->query();

        if ($isTrash == true)
            $language->onlyTrashed();

        if (isset($filter['active']))
            $language->where('active', $filter['active']);

        if (isset($filter['created_by']))
            $language->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $language->when($filter['q'], function ($language, $q) {
                $language->where('iso_codes', 'like', '%'.$q.'%')
                        ->orWhere('name', 'like', '%'.$q.'%')
                        ->orWhere('code', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $language->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $language->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $language->paginate($limit);
        } else {
            $result = $language->get();
        }

        return $result;
    }

    /**
     * Get Language Active
     * @param boolean $multiple
     */
    public function getLanguageActive($multiple = true)
    {
        $language = $this->languageModel->query();
        $language->select('id', 'iso_codes', 'name', 'active');

        $language->active();

        if ($multiple == false)
            $language->where('iso_codes', App::getLocale());

        $result = $language->get();

        return $result;
    }

    /**
     * Get Language One
     * @param array $where
     * @param array $with
     */
    public function getLanguage($where, $with = [])
    {
        $language = $this->languageModel->query();

        if (!empty($with))
            $language->with($with);

        $result = $language->firstWhere($where);

        return $result;
    }

    /**
     * Create Language
     * @param array $data
     */
    public function store($data)
    {
        try {

            $iso = Str::lower($data['iso_codes']);
            $language = $this->languageModel->create([
                'iso_codes' => $iso,
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'] ?? null,
                'time_zone' => $data['time_zone'] ?? null,
                'gmt' => $data['gmt'] ?? null,
                'active' => (bool)$data['active'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            $path = resource_path('lang/'.$iso);
            File::makeDirectory($path, $mode = 0777, true, true);
            File::copyDirectory(resource_path('lang/'.App::getLocale()), resource_path('lang/'.$iso));

            return $this->success($language,  __('global.alert.create_success', [
                'attribute' => __('feature/language.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Language
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $language = $this->getLanguage($where);

        try {
            
            $oldIso = $data['old_iso'];
            $iso = Str::lower($data['iso_codes']);
            $language->update([
                'iso_codes' => $iso,
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'] ?? null,
                'time_zone' => $data['time_zone'] ?? null,
                'gmt' => $data['gmt'] ?? null,
                'active' => (bool)$data['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $language['updated_by'],
            ]);

            if ($iso != $oldIso) {

                File::deleteDirectory(resource_path('lang/'.$oldIso));

                $path = resource_path('lang/'.$iso);
                File::makeDirectory($path, $mode = 0777, true, true);
                File::copyDirectory(resource_path('lang/'.App::getLocale()), resource_path('lang/'.$iso));
            }

            return $this->success($language,  __('global.alert.update_success', [
                'attribute' => __('feature/language.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Activate Language
     * @param array $where
     */
    public function activate($where)
    {
        $language = $this->getLanguage($where);

        try {
            
            $language->update([
                'active' => !$language['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $language['updated_by'],
            ]);

            return $this->success($language, __('global.alert.update_success', [
                'attribute' => __('feature/language.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Language
     * @param array $where
     */
    public function trash($where)
    {
        $language = $this->getLanguage($where);
        
        try {
            
            if ($language['locked'] == 0) {
        
                if (Auth::guard()->check()) {
                    $language->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $language->delete();

                return $this->success($language,  __('global.alert.delete_success', [
                    'attribute' => __('feature/language.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('feature/language.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Language
     * @param array $where
     */
    public function restore($where)
    {
        $language = $this->languageModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $language->restore();

            return $this->success($language, __('global.alert.restore_success', [
                'attribute' => __('feature/language.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Language (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $language = $this->languageModel->onlyTrashed()->firstWhere($where);
        } else {
            $language = $this->getLanguage($where);
        }

        try {
                
            File::deleteDirectory(resource_path('lang/'.$language['iso_codes']));

            $language->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('feature/language.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}