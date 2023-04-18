<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AboutRole
{
    /**
     * get the id role admin
     * @return int
     */
    public function idRoleAdmin() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_admin']
        )->id;
    }

    /**
     * get the id role event manager
     * @return int
     */
    public function idRoleEventManager() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_event_manager']
        )->id;
    }

    /**
     * get the id role moderator
     * @return int
     */
    public function idRoleModerator() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_moderator']
        )->id;
    }
}