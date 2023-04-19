|> index : 
    -> role admin (all)
    -> role taskManager (all in services)

|> show : 
    -> role admin (task, all), 
    -> role taskManager (task, all task created)
    -> role moderator (task)

|> store : 
    -> role admin (permis), 
    -> role eventManager (permis)
    -> role moderator (not authorized)

|> update : 
    -> role admin (all), 
    -> role eventManager (task created)
    -> role moderator (not authorized)

|> destroy : 
    -> role admin (all), 
    -> role eventManager (task created)
    -> role moderator (not authorized)