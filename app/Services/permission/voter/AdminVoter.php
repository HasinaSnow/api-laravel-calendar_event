<?php
namespace App\Services\Permission\Voter;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminVoter implements VoterInterface
{

    /**
     * verifie if the voter can vote
     * 
     * @param array $attributes 
     * @param mixed $subject the subject of the permission
     * @return bool
     */
    public function support (array $attributes, $subject = null) : bool
    {
        return true;
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
       $idRoleAdmin = (DB::select(
            'SELECT user_id
            FROM permission_user
            INNER JOIN permissions
                ON permissions.id = permission_user.permission_id
            WHERE permissions.name = ?',
            ['role_admin']
        ));

        if(empty($idRoleAdmin))
            return false;

        foreach($idRoleAdmin as $once)
           $roles[] = $once->user_id;

        return (in_array($dataUser['id'], $roles));

    }

}