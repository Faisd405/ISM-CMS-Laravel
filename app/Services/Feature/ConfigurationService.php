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
     */
    public function getConfigList($filter = [])
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
     * @param arrray $requestName
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
}