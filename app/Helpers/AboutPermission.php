<?php

namespace App\Helpers;

use App\Models\Permission;

class AboutPermission
{
    /**
     * Get all the id users of the specified permission
     * @param int $idPermission
     * @return array $Users collecrion's array of users
     */
    public function getArrayUserCollections(array $namePermissions) : array
    {
        $Permissions = Permission::whereIn('name', $namePermissions)->get();

        foreach($Permissions as $Permission)
        {
            $users = $Permission->users()->get();
            foreach($users as $user)
                $userCollections[] = $user;
        }

        return $userCollections;
    }

    /**
     * Get all the name permissions of the specified user
     * @param int $idUser
     * @return array $nameUsers
     */
    public function getNameUsers(int $idPermission) : array
    {
        $userPermissions = Permission::find($idPermission)->users()->get();
        foreach($userPermissions as $userPermission)
        {
            $nameUsers[] = $userPermission->name;
        }

        return $nameUsers;
    }

}