<?php
namespace App\Services\Permission\Voter;

use App\Models\Category;
use App\Models\Client;
use App\Models\Equipement;
use App\Models\Place;
use App\Models\Task;
use App\Models\Type;
use Illuminate\Support\Facades\DB;

class InteractVoter implements VoterInterface
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
            in_array(self::INTERACT, $attributes) && 
           ( 
                $subject instanceof Client ||
                $subject instanceof Category ||
                $subject instanceof Place ||
                $subject instanceof Type
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

        $idInteracted = DB::selectOne(
            'SELECT created_by, updated_by
            FROM ' . $subject->getTable() .
            ' WHERE id = ?',
            [$subject->id]
        );

        return ($dataUser['id'] === $idInteracted->created_by || $dataUser['id'] === $idInteracted->updated_by );

    }

}