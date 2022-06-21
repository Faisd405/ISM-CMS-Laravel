<?php

namespace App\Services\Feature;

use App\Models\Feature\Notification;
use App\Traits\ApiResponser;

class NotificationService
{
    use ApiResponser;

    private $notifModel;

    public function __construct(
        Notification $notifModel
    )
    {
        $this->notifModel = $notifModel;
    }
    
    /**
     * Get Notif List
     * @param array $filter
     * @param booleean $withPaginate
     * @param int $limit
     * @param array $with
     * @param array $orderBy
     */
    public function getNotificationList($filter = [], $withPaginate = true, $limit = 10,
        $with = [], $orderBy = [])
    {
        $notif = $this->notifModel->query();

        if (isset($filter['user_to']))
            $notif->whereJsonContains('user_to', $filter['user_to']);
            
        if (isset($filter['q']))
            $notif->when($filter['q'], function ($notif, $q) {
                $notif->where('attribute->title', 'like', '%'.$q.'%')
                    ->orWhere('attribute->content', 'like', '%'.$q.'%');
            });

        if (isset($filter['limit']))
            $limit = $filter['limit'];

        if (!empty($with))
            dd($with);
            $notif->with($with);

        if (!empty($orderBy))
            foreach ($orderBy as $key => $value) {
                $notif->orderBy($key, $value);
            }
        
        if ($withPaginate == true) {
            $result = $notif->paginate($limit);
        } else {

            if ($limit > 0)
                $notif->limit($limit);

            $result = $notif->get();
        }

        return $result;
    }

    /**
     * Get Latest Notif
     * @param int $userId
     * @param int $limit
     */
    public function latestNotification($userId, $limit = 5)
    {
        $notif = $this->notifModel->query();

        $notif->whereJsonContains('user_to', $userId);

        $result = $notif->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->map(function($notif) {
                return $this->formatNotif($notif);
            });

        return $result;
    }

    /**
     * Total Unread
     * @param int $userId
     */
    public function totalUnread($userId)
    {
        $notif = $this->notifModel->query();

        $notif->whereJsonContains('user_to', $userId);
        $notif->whereJsonDoesntContain('read_by', $userId);

        $result = $notif->count();

        return $result;
    }

    /**
     * Get Notif One
     * @param array $where
     * @param array $with
     */
    public function getNotif($where, $with = [])
    {
        $notif = $this->notifModel->query();
        
        if (!empty($with))
            $notif->with($with);

        $result = $notif->firstWhere($where);

        return $result;
    }

    /**
     * Send Notif
     * @param array $data
     */
    public function sendNotif($data)
    {
        $notif = $this->notifModel->create($data);

        return $notif;
    }

    /**
     * Read Notif
     * @param int $usrId
     * @param array $where
     */
    public function readNotif($userId, $where)
    {
        $notif = $this->getNotif($where);

        $readby = [];
        if(!empty($notif['read_by']))
            $readby = $notif['read_by'];

        if(!in_array($userId, $readby))
            array_push($readby, $userId);
        
        $notif->update(['read_by' => $readby]);

        // $this->deleteNotifIfAllRead($where);
    }

    /**
     * Delete Notif (Permanen)
     * @param array $where
     */
    private function deleteNotifIfAllRead($where)
    {
        $notif = $this->getNotif($where);

        $read = $notif->read_by;
        $sent = $notif->user_to;

        // if($read > $sent) {
        //     $find->delete();
        // }
    }

    /**
     * Format Notif
     * @param model $notif
     */
    public function formatNotif($notif)
    {
        return [
            'id' => $notif['id'],
            'from' => !empty($notif['user_from']) && !empty($notif['userFrom']) ? 
                $notif['userFrom']['name'] : __('global.visitor'),
            'icon' => $notif['attribute']['icon'],
            'color' => $notif['attribute']['color'],
            'title' => $notif['attribute']['title'],
            'content' => $notif['attribute']['content'],
            'link' => $notif['link'],
            'date' => $notif['created_at']->diffForHumans()
        ];
    }
}