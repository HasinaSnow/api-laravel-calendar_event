## SYSTEME DE PERMISSION 
    -> adminVoter : 
        - can( '...', '...' ) , 
        - vote('user => role_admin')

    # PERMISSION_USER
    -> createPermissionUserVoter
        - can (['create'], permissionUser)        
        - voter([
            * user => role_permission_user_manager
        ])

    # SERVICE
    - responsable des users dans un ou plusieurs services
    -> CreateServiceUserVoter
        - can (['create'], serviceUser)        
        - voter([
            * user => role_service_user_manager 
        ])
    
    -> interactServiceUserVoter
        - can (['create'], serviceUser)        
        - voter([
            * user => role_service_user_manager && 
            * user = serviceUser->(created_by || updated_by)
        ])

    # EVENT
    -> createEventVoter
        - can (
            * ['create'], 
            * [Event || Client || Place || Task || PrivateInfos || budget || deposit || invoice ]
        )        
        - voter([
            * user => role_event_manager
        ])

    -> interactEventVoter
        - can (
            * ['interact'], 
            * [Event || Client || Place || type || Category || Task || PrivateInfos] 
        )        
        - voter([
            * user = event->user
            * user = client->user
            * user = place->user
            * user = type->user
            * user = task->user
            * user = PrivateInfos->user
            * user = budget->user
            * user = deposit->user
            * user = invoice->user
        ])

    -> privateEventVoter
        -> can('private', Event)
        -> vote (user = event->private->user)

    # BUDGET

    
## TACHE TERMINEES

# controllers
- accountcontroller
- clientController
- eventcontroller
- placeController
- typeController
- confirmationController
- serviceUserController
- eventServiceController
- serviceUserController
- budgetController
- depositController

## TACHES A FAIRE

# controllers
- paymentController
- taskController
- eventTaskController
- invoicescontroller
- equipementController
- equipementEventController
- permission (not)



## creation d'event
- date (store)
=> choix (index)
    - service
    - categ
    - type
    - confirm
=> creation ou choix (index/store)
    - place
    - client
=> creation (store)
    - budget
        => creation (store)
            - payments


## principe de tache
- création de tache (createTaskEventVoter, isAdminVoter)