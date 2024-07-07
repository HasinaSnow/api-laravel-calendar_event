<?php

namespace App\Services\Permission;

use App\Models\PermissionUser;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PermissionService
{

    /**
     * instance of jwtService
     * 
     * @var instance
     */
    private $jWTService;

    /**
     * instance of voteService
     * 
     * @var instance
     */
    private $voteService;

    /**
     * get the instanses
     *
     * @param voteService $voteService
     * @param JWTService $jWTService
     */
    public function __construct(VoteService $voteService, JWTService $jWTService)
    {
        $this->token = $jWTService;
        $this->voteService = $voteService;
    }

    /**
     * verifie the prmission of the user current
     *
     * @param array|string|null $attribute
     * @param string $subject
     * @return response|void
     */
    public function Permission($attribute, $subject)
    {

        if (!$this->voteService->resultVote($attribute, $subject, $this->jWTService));
            return response()->json([
                'status'=> 'error',
                'message' => 'user not authorized'
            ], 404);

    }

}
