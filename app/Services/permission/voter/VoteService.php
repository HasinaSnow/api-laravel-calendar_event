<?php
namespace App\Services\Permission\Voter;

use App\Models\User;

class VoteService
{

    /**
     * array of voter
     *
     * @var Voter[]
     */
    private $voters = [];

    /**
     * instance of JWTService
     *
     * @var instance
     */

    public function __construct(array $voters)
    {
        $this->voters = $voters;
    }

    /**
     * verifie a permisssion of user
     *
     * @param User $user
     * @param string $permission
     * @param mixed $subject
     * @return boolean
     */
    public function resultVote(array|string|null $attributes, $subject, $JWTService) : bool
    {
        foreach($this->voters as $voter)
        {
            $votes[] = $this->voterCan($voter, $attributes, $subject, $JWTService);
        }

        return in_array(true, $votes);

    }


    private function voterCan($voter, $attributes, $subject, $JWTService)
    {
        if($voter->support($attributes, $subject))
            return $voter->vote($attributes, $subject, $JWTService->getDataUserToken());
        return false;
    }

}