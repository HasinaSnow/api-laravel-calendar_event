<?php

namespace App\Helpers;

use App\Models\User;
use App\Services\JWT\JWTService;
use App\Services\Permission\Voter\VoteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AboutCurrentUser
{

    /**
     * instance of jwtservice
     */
    private $jWTService;

    /**
     * instance of voteService
     */
    private $voteService;


    public function __construct(JWTService $jWTService, VoteService $voteService)
    {
        $this->jWTService = $jWTService;
        $this->voteService = $voteService;
    }

    /**
     * verifie if the current user is permis to create the specified model 
     * 
     * @param Illuminate\Database\Eloquent\Model $model 
     * @return boolean
     */
    public function isPermisToCreate(Model $model): bool
    {
        return $this->voteService->resultVote(['create'] , $model, $this->jWTService);
    }

    /**
     * verifie if the current user is permis to interact on the specified model 
     * 
     * @param Illuminate\Database\Eloquent\Model $model 
     * @return boolean
     */
    public function isPermisToInteract(Model $model): bool
    {
        return $this->voteService->resultVote(['interact'] , $model, $this->jWTService);
    }

    /**
     * verify if the current user have a role admin
     * 
     * @return boolean
     */
    public function isAdmin(): bool
    {
        return in_array(AboutRole::admin(), $this->idRoles());
    }

    /**
     * verify if the current user have a role eventManager
     * 
     * @return boolean
     */
    public function isEventManager(): bool
    {
        return in_array(AboutRole::eventManager(), $this->idRoles());
    }

    /**
     * verify if the current user have a role eqipementManager
     * 
     * @return boolean
     */
    public function isTaskManager(): bool
    {
        return in_array(AboutRole::taskManager(), $this->idRoles());
    }

    /**
     * verify if the current user have a role eventManager
     * 
     * @return boolean
     */
    public function isEquipementManager(): bool
    {
        return in_array(AboutRole::equipementManager(), $this->idRoles());
    }

    /**
     * verify if the current user have a role moderator
     * 
     * @return boolean
     */
    public function isModerator(): bool
    {
        return in_array(AboutRole::moderator(), $this->idRoles());
    }
    
    /**
     * verify if the current user is a creator of the type de model
     * 
     * @return bool
     */
    public function isCreator(Model $model): bool
    {
        return $model->created_by === $this->id();
    }

    /**
     * get the id of the current user
     * @return int
     */
    public function id(): int
    {
        return $this->jWTService->getIdUserToken();
    }

    /**
     * get the id of the current user
     * @return string
     */
    public function name(): string
    {
        return User::find($this->jWTService->getIdUserToken())->name;
    }

    /**
     * get the id roles of user current
     * @param int $idUser
     * @return array
     */
    public function idRoles(): array
    {
        $idUserRole = DB::select(
            'SELECT permission_id
            FROM permission_user
            WHERE user_id = ?',
            [$this->jWTService->getIdUserToken()]
        );
        foreach ($idUserRole as $once)
            $userRoles[] = $once->permission_id;

        return $userRoles;
    }

    /**
     * get the id services of user current
     * @param int $idUser
     * @return int[]
     */
    public function idServices(): array
    {
        $idUserServices = DB::select(
            'SELECT service_id
            FROM service_user
            WHERE user_id = ?',
            [$this->jWTService->getIdUserToken()]
        );

        foreach ($idUserServices as $once)
            $userServices[] = $once->service_id;
        return $userServices;
    }

    /**
     *  Get the id services of user specified
     * @param int $user the user specified
     * 
     * @return array $idServices
     */
    public function idServicesOfUserSpecified(int $idUser): array
    {
        $services = User::findorfail($idUser)->services()->get(['id'])->toArray();
        foreach($services as $service)
        {
            $idServices[] = $service['id'];
        }
        return $idServices;
    }
}
