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
     * get the id role task manager
     * @return int
     */
    public  static function taskManager() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_task_manager']
        )->id;
    }

    /**
     * get the id role equipement manager
     * @return int
     */
    public  static function equipementManager() : int
    {
        return DB::selectOne(
            'SELECT id
            FROM permissions
            WHERE name = ?',
            ['role_equipement_manager']
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