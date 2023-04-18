<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AboutUser 
{
    /**
     * get the id roles of user current
     * @param int $idUser
     * @return array
     */
    public function idUserRoles(int $idUser) : array
    {
        $idUserRole = DB::select(
            'SELECT permission_id
            FROM permission_user
            WHERE user_id = ?',
            [$idUser]
        );
        foreach($idUserRole as $once)
        $userRoles[] = $once->permission_id;

        return $userRoles;
    }

    /**
     * get the id services of user current
     * @param int $idUser
     * @return array
     */
    public function idUserServices(int $idUser) : array
    {
        $idUserServices = DB::select(
            'SELECT service_id
            FROM service_user
            WHERE user_id = ?',
            [$idUser]
        );

        foreach($idUserServices as $once)
            $userServices[] = $once->service_id;

        return $userServices;
    }


}