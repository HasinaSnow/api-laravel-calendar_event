<?php
namespace App\Services\Permission\Voter;

use App\Models\Service;
use Illuminate\Support\Facades\DB;

class InteractServiceUserVoter implements VoterInterface
{
    const INTERACT = 'interact';

    /**
     * verifie if the voter can vote
     * 
     * @param array $attributes 
     * @param mixed $subject the subject of the permission
     * @return bool
     */
    public function support (array $attributes, $subject = null) : bool
    {
        return (
            in_array(self::INTERACT, $attributes ) && 
            (
                $subject instanceof Service 
            )
        );
    }

    /**
     * the voter vote
     *
     * @param string $token
     * @param array $attributes
     * @param mixed $subject subject of the permission
     * @return boolean
     */
    public function vote(array $attributes, $subject = null, array $dataUser) : bool
    {
        $idRoleServiceManager = DB::select(
            'SELECT user_id
            FROM permission_user
            INNER JOIN permissions
                ON permissions.id = permission_user.permission_id
            WHERE permissions.name = ?',
            ['role_service_user_manager']
        );

        if(empty($idRoleServiceManager))
            return false;

        foreach($idRoleServiceManager as $once)
           $roles[] = $once->user_id;

        return (
            in_array($dataUser['id'], $roles) &&
            ($dataUser['id'] === $subject->created_by ||
            $dataUser['id'] === $subject->updated_by )
        );

    }

}