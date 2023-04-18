<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AboutRole
{

    /**
     * get the id role admin
     * @return int
     */
    public static function admin() : int
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
    public  static function eventManager() : int
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
    public static function moderator() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_moderator']
        )->id;
    }
}