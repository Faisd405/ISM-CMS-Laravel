<?php

namespace App\Services\Feature;

use App\Models\Feature\Registration;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Support\Facades\Auth;

class RegistrationService
{
    use ApiResponser;

    private $registrationModel;

    public function __construct(
        Registration $registrationModel
    )
    {
        $this->registrationModel = $registrationModel;
    }

    /**
     * Get Registration List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param boolean $isTrash
     * @param array $with
     * @param array $orderBy
     */
    public function getRegistrationList($filter = [], $withPaginate = true, $limit = 10, 
        $isTrash = false, $with = [], $orderBy = [])
    {
        $registration = $this->registrationModel->query();

        if ($isTrash == true)
            $registration->onlyTrashed();

        if (isset($filter['type']))
            $registration->where('type', $filter['type']);

        if (isset($filter['status']))
            $registration->where('active', $filter['status']);

        if (isset($filter['created_by']))
            $registration->where('created_by', $filter['created_by']);

        if (isset($filter['q']))
            $registration->when($filter['q'], function ($registration, $q) {
                $registration->where('name', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            $registration->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $registration->orderBy($key, $value);
            }

        if ($withPaginate == true) {
            $result = $registration->paginate($limit);
        } else {

            if ($limit > 0)
                $registration->limit($limit);

            $result = $registration->get();
        }

        return $result;
    }

    /**
     * Get Registration One
     * @param array $where
     * @param array $with
     */
    public function getRegistration($where, $with = [])
    {
        $registration = $this->registrationModel->query();

        if (!empty($with))
            $registration->with($with);

        $result = $registration->firstWhere($where);

        return $result;
    }

    /**
     * Create Registration
     * @param array $data
     */
    public function store($data)
    {
        try {

            $registration = $this->registrationModel->create([
                'name' => $data['name'],
                'type' => $data['type'],
                'roles' => $data['roles'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'active' => (bool)$data['active'],
                'created_by' => Auth::guard()->check() ? Auth::user()['id'] : null,
            ]);

            return $this->success($registration,  __('global.alert.create_success', [
                'attribute' => __('feature/registration.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Update Registration
     * @param array $data
     * @param array $where
     */
    public function update($data, $where)
    {
        $registration = $this->getRegistration($where);

        try {
            
            $registration->update([
                'name' => $data['name'],
                'type' => $data['type'],
                'roles' => $data['roles'] ?? null,
                'start_date' => $data['start_date'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'active' => (bool)$data['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $registration['updated_by'],
            ]);

            return $this->success($registration,  __('global.alert.update_success', [
                'attribute' => __('feature/registration.caption')
            ]));


        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Activate Registration
     * @param array $where
     */
    public function activate($where)
    {
        $registration = $this->getRegistration($where);

        try {
            
            $registration->update([
                'active' => !$registration['active'],
                'updated_by' => Auth::guard()->check() ? Auth::user()['id'] : $registration['updated_by'],
            ]);

            return $this->success($registration, __('global.alert.update_success', [
                'attribute' => __('feature/registration.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Trash Registration
     * @param array $where
     */
    public function trash($where)
    {
        $registration = $this->getRegistration($where);
        
        try {
            
            if ($registration['locked'] == 0) {
        
                if (Auth::guard()->check()) {
                    $registration->update([
                        'deleted_by' => Auth::user()['id']
                    ]);
                }

                $registration->delete();

                return $this->success($registration,  __('global.alert.delete_success', [
                    'attribute' => __('feature/registration.caption')
                ]));
    
            } else {
                return $this->error(null,  __('global.alert.delete_failed_used', [
                    'attribute' => __('feature/registration.caption')
                ]));
            }

        } catch (Exception $e) {
            
            return $this->error(null,  $e->getMessage());
        }
    }

    /**
     * Restore Registration
     * @param array $where
     */
    public function restore($where)
    {
        $registration = $this->registrationModel->onlyTrashed()->firstWhere($where);

        try {
            
            //restore data yang bersangkutan
            $registration->restore();

            return $this->success($registration, __('global.alert.restore_success', [
                'attribute' => __('feature/registration.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }

    /**
     * Delete Registration (Permanent)
     * @param array $where
     */
    public function delete($request, $where)
    {
        if ($request->get('is_trash') == 'yes') {
            $registration = $this->registrationModel->onlyTrashed()->firstWhere($where);
        } else {
            $registration = $this->getRegistration($where);
        }

        try {
            
            $registration->forceDelete();

            return $this->success(null,  __('global.alert.delete_success', [
                'attribute' => __('feature/registration.caption')
            ]));
            
        } catch (Exception $e) {
            
            return $this->error(null, $e->getMessage());
        }
    }
}