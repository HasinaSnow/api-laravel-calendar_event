<?php
namespace App\Services\Permission\Voter;

use App\Models\Equipement;

use Illuminate\Support\Facades\DB;

class InteractEquipementVoter implements VoterInterface
{
    private const INTERACT = 'interact';

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
            $subject instanceof Equipement
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
            ['role_equipement_manager']
        );

        // verify the user(s) who have the role_event_manager
        if(empty($idRoleServiceManager))
            return false;

        foreach($idRoleServiceManager as $once)
            $roles[] = $once->user_id;

        // verify if the user current have the role_event_manager
        if(in_array($dataUser['id'], $roles))
        {
            // verify if it about the attribute = 'interact'
            if(in_array(self::INTERACT, $attributes))
            {
                return (
                    ($dataUser['id'] === $subject->created_by ||
                    $dataUser['id'] === $subject->updated_by )
                );
            }
            else
            {
                return true;
            }
        }else{
            return false;
        }    


    }

}