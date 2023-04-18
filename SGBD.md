## CREATION DE EVENT
|- role 
    - event_manager

|- voters activés
    - CreateEventVoter

|- créations
    - event (date)
    - client (1)
    - taches (2)
    - budget 
    - deposit
    - payments
    - facture

|- selections
    - unique
        - audience (public/private)
        - type
        - client (1)
        - categ
        - confirm
        - lieu
    - multiple (après enregistrement de base)
        - taches (2)
        - equipements [par secteurs]


## MODIFICATION DE EVENT
|- role 
    - event_manager

|- voters activés
    - CreateEventVoter

|- modifications
    - event (date)
    - client (1)
    - taches (2)
    - budget 
    - deposit
    - payments
    - portée (public/private)

|- selections
    - unique
        - audience (public/private) 
        - type
        - client (1)
        - categ
        - confirm
        - lieu
    - multiple
        - services
        - taches (2)
        - equipements [par secteurs]

## SUPPRESSION DE EVENT
|- role 
    - event_manager

|- voters activés
    - interactEventVoter (possede le role et interagir seulement sur les event créer par l'user)

|- suppression en cascade ()
    - event_service (event_id, service_id)
        - event (date, audience, category_id, place_id, confirm_id, type_id, budget_id, client_id) -> Event
    - budgets (amount, infos, payment_id, deposit_id) -> Budget
        - payment (type, infos, isPaid, paid_at) -> Payment
        - deposit (amount, expiration, rate, payment_id) -> Deposit
            - payment (type, infos, isPaid, paid_at) -> Payments
    - Event_tasks (event_id, task_id, check, attribute_to) -> EventTask
    - equip_event (equip_id, event_id, amount, quantity) -> EquipementEvent
    - facture (reference, event_id)
