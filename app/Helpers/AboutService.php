<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AboutService 
{
    /**
     * get the id services of an event 
     * @param int $idEvent
     * @return array
     */
    public function idServicesForEvent(int $idEvent) : array
    {
        $idServicesForEvent = DB::select(
            'SELECT permission_id
            FROM permission_users
            WHERE user_id = ?',
            [$idEvent]
        );
        foreach($idServicesForEvent as $once)
        $idServices[] = $once->permission_id;

        return $idServices;
    }

    public function eventInServiceUser(int $idEvent, int $idUser)
    {
        $serviceUsers = DB::select(
            'SELECT service_id
            FROM service_users
            WHERE user_id = ?',
            [$idUser]
        );
        foreach($serviceUsers as $once)
        $idServiceUser[] = $once->service_id;

        $serviceEvents = DB::select(
            'SELECT service_id
            FROM event_services
            WHERE event_id = ?',
            [$idEvent]
        );
        foreach($serviceEvents as $once)
            $idServicesEvent[] = $once->service_id;
        
        foreach($idServicesEvent as $once)
            $result[] = (in_array($once, $idServiceUser));

        return in_array(true, $result);
    }

    
}