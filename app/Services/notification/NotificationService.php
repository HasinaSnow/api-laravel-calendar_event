<?php

namespace App\Services\Notification;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * store the notifications in the database
     * @param object $notification 
     * @param array $users array of user's collections
     */
   public function store(object $notification, array $users)
   {
        foreach($users as $user)
        {
            $user->notify($notification);
        }
   }

   /**
    * Get the notifications in the database for the specified users
    * @param model $user model of user
    * @return array $notifications[]
    */
   public function get(Model $user): array
   {
        $notifications = $user->unreadNotifications()->get()->toArray();
        $user->unreadNotifications->markAsRead();

        return $notifications;
   }

}