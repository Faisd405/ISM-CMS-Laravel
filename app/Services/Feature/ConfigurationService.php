<?php

namespace App\Services\Feature;

use App\Models\Feature\Configuration;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConfigurationService
{
    use ApiResponser;

    private $configModel;

    public function __construct(
        Configuration $configModel
    )
    {
        $this->configModel = $configModel;
    }

    /**
     * Get Config List
     * @param array $filter
     * @param array $orderBy
     */
    public function getConfigList($filter = [], $orderBy = [])
    {
        $config = $this->configModel->query();

        if (isset($filter['group']))
            $config->where('group', $filter['group']);

        if (isset($filter['is_upload']))
            $config->where('is_upload', $filter['is_upload']);

        if (isset($filter['show_form']))
            $config->where('show_form', $filter['show_form']);

        if (isset($filter['active']))
            $config->where('active', $filter['active']);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $config->orderBy($key, $value);
            }

        $result = $config->get();

        return $result;
    }

    /**
     * Get Config One
     * @param array $where
     * @param array $with
     */
    public function getConfig($where, $with = [])
    {
        $config = $this->configModel->query();
        
        if (!empty($with))
            $config->with($with);

        $result = $config->firstWhere($where);

        return $result;
    }

    /**
     * Get Config Value
     * @param string $name
     */
    public function getConfigValue($name)
    {
        $config = $this->configModel->select('value')
            ->firstWhere('name', $name)['value'];

        return $config;
    }
    
    /**
     * Get Config File
     * @param string $name
     */
    public function getConfigFile($name)
    {
        $config = $this->getConfig(['name' => $name]);

        if (!empty($config['value'])) {
            $file = Storage::url(config('cms.files.config.path').$config['value']);
        } else {
            $file = asset(config('cms.files.config.'.$name.'.file'));
        }

        return $file;
    }

    /**
     * Set config cache
     */
    public function setConfigCache()
    {
        $setConfigs = [];
        foreach ($this->configModel->active()->get() as $key => $value) {
            if ($value['is_upload'] == 1) {
                $setConfigs[$value['name']] = $this->getConfigFile($value['name']);
            } else {
                $setConfigs[$value['name']] = $this->getConfigValue($value['name']);
            }
        }

        $config = app('config');
        $config->set('cmsConfig', $setConfigs);
    }
    
    /**
     * Create Confg
     * @param array $data
     */
    public function storeConfig($data)
    {
        try {
            
            $config = new Configuration;
            $config->group = $data['group'];
            $config->name = Str::slug($data['name'], '_');
            $config->label = $data['label'];
            $config->value = $data['value'] ?? null;
            $config->is_upload = (bool)$data['is_upload'];
            $config->show_form = (bool)$data['show_form'];
            $config->active = (bool)$data['active'];
            $config->locked = (bool)$data['locked'];
            $config->save();

            return $this->success($config,  __('global.alert.create_success', [
                'attribute' => __('feature/configuration.website.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Set Config
     * @param arrray $data
     */
    public function setConfig($data)
    {
        try {
            
            foreach ($data['label'] as $key => $value) {
                $this->getConfig(['name' => $key])->update([
                    'label' => $value,
                    'show_form' => isset($data['show_form'][$key]) ? 1 : 0,
                    'active' => isset($data['active'][$key]) ? 1 : 0,
                    'locked' => isset($data['locked'][$key]) ? 1 : 0,
                ]);
            }

            return $this->success($data,  __('global.alert.update_success', [
                'attribute' => __('feature/configuration.website.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Config
     * @param arrray $requestName
     */
    public function updateConfig($requestName)
    {
        try {
            
            foreach ($requestName as $key => $value) {
                $this->getConfig(['name' => $key])->update([
                    'value' => $value
                ]);
            }

            return $this->success($requestName,  __('global.alert.update_success', [
                'attribute' => __('feature/configuration.website.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Upload File Config
     * @param arrray $requestName
     */
    public function uploadFileConfig($request, $name)
    {
        try {
            
            if ($request->hasFile($name)) {

                $file = $request->file($name);
                $fileName = Str::replace(' ', '-', $file->getClientOriginalName());
    
                Storage::delete(config('cms.files.config.path').
                    $request->input('old_'.$name));
                Storage::put(config('cms.files.config.path').
                    $fileName, file_get_contents($file));
    
                $config = $this->getConfig(['name' => $name])->update([
                        'value' => $fileName
                    ]);
    
                return $this->success($config,  __('global.alert.update_success', [
                    'attribute' => __('feature/configuration.website.caption')
                ]));
    
            }

            return $this->error($name,  __('global.alert.update_failed', [
                'attribute' => __('feature/configuration.website.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Delete File Config
     * @param string $name
     */
    public function deleteFileConfig($name)
    {
        $config = $this->getConfig(['name' => $name]);
        
        try {
            
            Storage::delete(config('cms.files.config.path').$config['value']);

            $config->update([
                'value' => null,
            ]);

            return $this->success($config,  __('global.alert.delete_success', [
                'attribute' => __('feature/configuration.website.caption')
            ]));

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Delete Config
     * @param string $nam
     */
    public function deleteConfig($name)
    {
        $config = $this->getConfig(['name' => $name]);
        
        try {

            if ($config['locked'] == 0) {
                
                $config->delete();

                return $this->success($config,  __('global.alert.delete_success', [
                    'attribute' => __('feature/configuration.website.caption')
                ]));

            } else {
                
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('feature/configuration.website.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }
}