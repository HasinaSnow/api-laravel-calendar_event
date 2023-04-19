|> index : 
    -> role admin (all)
    -> role equipementManager (all in services)

|> show : 
    -> role admin (equipement, all), 
    -> role equipementManager (equipement, all equipement created in services)
    -> role moderator (equipement)

|> store : 
    -> role admin (permis), 
    -> role equipementManager (permis)
    -> role moderator (not authorized)

|> update : 
    -> role admin (all), 
    -> role equipementManager (equipement created)
    -> role moderator (not authorized)

|> destroy : 
    -> role admin (all), 
    -> role equipementManager (equipement created)
    -> role moderator (not authorized)