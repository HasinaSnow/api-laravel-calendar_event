## A FAIRE :
|> creation automatique de invoice après creation d'un event
|> upload de fichier (jpg)
## EN COURS :
|> installer et apprendre git

## FINI :
|> migrations et factory des tables (sauf la table invoices)
|> seed des tables simple set pivots (sauf la table invoices)
|> eventController
    index(){
        role_admin 
            => (client | type | categ | place | confirm | service) | budget,
        role_event_manager 
            => (client | type | categ | place | confirm | service) | audience = true or created_by = userId  ,
        role_moderator 
            => client | type | categ | place | confirm | service = userIdService and audience = true,
    }
    store(){
        => (date, audience, client_id, place_id, categ_id, confirm_id, type_id)
        => selection multiple sur le service_id
        => la creation de budget
        => la creation de nouveau client ou place se fera en amont
    }
    create(){
        => renvoyer tous les datas necessaires à la creation d'un event
            - services, clients, types, confirmations, categs, places
    }
    edit(){
    => renvoyer tous les datas necessaires à la modification d'un event
        - services, clients, types, confirmations, categs, places ...
    }
    update(){
        => (date, audience, client_id, place_id, categ_id, confirm_id, type_id)
        => selection multiple sur le service_id
        => si le budget existe, l'user peut se contenter de le modifier ou la supprimer, sinon il peut creer un nouveau
        => la creation de nouveau client ou place se fera en amont
    }
    destroy(){
        => supprimer une ligne va aussi supprimer d'autres sur les tables en relation (à condition onDelete('cascade))
            - event_service, 
            - event_task, 
            - equipement_event, 
            - budget->payments->(remainders, deposits) 
            - invoice
    }