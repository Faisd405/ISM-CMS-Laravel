<?php

namespace App\Http\Controllers\Feature;

use App\Http\Controllers\Controller;
use App\Services\Feature\NotificationService;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    use ApiResponser;

    private $notifService;

    public function __construct(
        NotificationService $notifService
    )
    {
        $this->notifService = $notifService;
    }

    public function index(Request $request)
    {
        $url = $request->url();
        $param = Str::replace($url, '', $request->fullUrl());

        $filter['user_to'] = Auth::user()['id'];
        if ($request->input('q', '') != '') {
            $filter['q'] = $request->input('q');
        }
        if ($request->input('limit', '') != '') {
            $filter['limit'] = $request->input('limit');
        }

        $data['notifications'] = $this->notifService->getNotificationList($filter, true, 10, [], [
            'created_at' => 'DESC'
        ]);
        $data['no'] = $data['notifications']->firstItem();
        $data['notifications']->withPath(url()->current().$param);

        return view('backend.features.notification', compact('data'), [
            'title' => __('feature/notification.title'),
            'breadcrumbs' => [
                __('feature/notification.title') => '',
            ]
        ]);
    }

    public function latest()
    {
        $userId = Auth::user()->id;
        $total = $this->notifService->totalUnread($userId);
        $latest = $this->notifService->latestNotification($userId);

        return $this->success([
            'total' => $total,
            'latest' => $latest
        ]);
    }

    public function read($id)
    {
        try {
            
            $notif = $this->notifService->getNotif(['id' => $id]);
            $this->notifService->readNotif(Auth::user()['id'], ['id' => $id]);

            return redirect($notif['link']);

            // return back()->with('success', __('feature/notification.alert.success_read'));

        } catch (Exception $e) {

            return back()->with('failed', $e->getMessage());
        }
    }
}
