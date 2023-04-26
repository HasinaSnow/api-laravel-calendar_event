<?php

namespace App\Helpers;

use App\Models\User;

class AboutAllUsers
{
    /**
     * Get all the id permissions of the specified user
     * @param int $idUser
     * @return array $idPermissons
     */
    public function getIdPermisions(int $idUser) : array
    {
        $userPermissions = User::find($idUser)->permissions()->get();
        foreach($userPermissions as $userPermission)
        {
            $idPermissions[] = $userPermission->id;
        }

        return $idPermissions;
    }

    /**
     * Get all the name permissions of the specified user
     * @param int $idUser
     * @return array $idPermissons
     */
    public function getNamePermisions(int $idUser) : array
    {
        $userPermissions = User::find($idUser)->permissions()->get();
        foreach($userPermissions as $userPermission)
        {
            $namePermissions[] = $userPermission->name;
        }

        return $namePermissions;
    }

    /**
     * Get all the id services of the specified user
     * @param int $idUser
     * @return array $idServices
     */
    public function getIdServices(int $idUser) : array
    {
        $userServices = User::find($idUser)->services()->get();
        foreach($userServices as $userService)
        {
            $idServices[] = $userService->id;
        }

        return $idServices;
    }

    /**
     * Get all the name services of the specified user
     * @param int $idUser
     * @return array $nameServices
     */
    public function getNameServices(int $idUser) : array
    {
        $userServices = User::find($idUser)->services()->get();
        foreach($userServices as $userService)
        {
            $nameServices[] = $userService->id;
        }

        return $nameServices;
    }

}