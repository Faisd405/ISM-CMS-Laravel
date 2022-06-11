<?php

namespace App\Observers;

use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogObserver
{
    public function saved($model)
    {
        $log = new UserLog();
        $data = $log->logable()->associate($model);

        if (Auth::check() == true) {
            UserLog::create([
                'user_id' => Auth::user()->id,
                'event' => $model->wasRecentlyCreated == true ? 1 : 2,
                'logable_id' => $data['logable_id'],
                'logable_type' => $data['logable_type'],
                'logable_name' => $model->getTable(),
                'content' => $data['logable'],
                'ip_address' => request()->ip(),
            ]);
        }
    }

    public function deleting($model)
    {
        $log = new UserLog;
        $data = $log->logable()->associate($model);

        if (Auth::check() == true) {
            UserLog::create([
                'user_id' => Auth::user()->id,
                'event' => '0',
                'logable_id' => $data['logable_id'],
                'logable_type' => $data['logable_type'],
                'logable_name' => $model->getTable(),
                'content' => $data['logable'],
                'ip_address' => request()->ip(),
            ]);
        }
    }
}
