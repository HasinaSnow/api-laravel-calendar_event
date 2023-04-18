<?php
namespace App\Services\Permission\Voter;

interface VoterInterface
{
    /**
     * verifie if the voter can vote
     * 
     * @param array $attributes 
     * @param mixed $subject the subject of the permission
     * @return bool
     */
    public function support (array $attributes, $subject = null) : bool;

    /**
     * the voter vote
     *
     * @param array $dataUser
     * @param array $permission
     * @param mixed $subject subject of the permission
     * @return boolean
     */
    public function vote(array $attributes, $subject = null, array $dataUser) : bool;

}