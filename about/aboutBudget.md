|> index : 
    -> all user is permis

|> show : 
    -> role admin (place, all), 
    -> role eventManager (place, all event created)
    -> role moderator (place)

|> initilizePayment
    -> role admin
    -> choix (initialiser un deposit)

|> store : 
    -> role admin (permis), 
    -> role eventManager (permis)
    -> role moderator (not authorized)

|> update : 
    -> role admin (all), 
    -> role eventManager (place created)
    -> role moderator (not authorized)

|> destroy : 
    -> role admin (all), 
    -> role eventManager (place created)
    -> role moderator (not authorized)