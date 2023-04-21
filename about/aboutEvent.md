|> index : 
    -> role admin (all)
    -> role eventManager (all in services)

|> show : 
    -> role admin (event, all), 
    -> role eventManager (event, all event created in services)
    -> role moderator (event)

|> showTaskListAttached : 
    -> role admin et taskManager(tasks, all), 
    -> role viewver (task attribued_to user)

|> showTaskListNotAttached : 
    -> role admin et taskManager(tasks, all), 

|> attachTasks : 
    -> role admin et taskManager(tasks, all), 
    * lists
        -> attribute_to => optionnel
        -> check => optionnel
        -> expiration => optionnel

|> attributeTaskList
    -> role admin et taskmanager
    * lists
        -> expiration => optionnel

|> checkTaskList


|> store : 
    -> role admin (permis), 
    -> role eventManager (permis)
    -> role moderator (not authorized)

|> update : 
    -> role admin (all), 
    -> role eventManager (event created)
    -> role moderator (not authorized)



|> destroy : 
    -> role admin (all), 
    -> role eventManager (event created)
    -> role moderator (not authorized)