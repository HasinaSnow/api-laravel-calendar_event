|> initializePayment : 
    -> role admin, 
    -> systèm :
        - si payment exist dejà pour le budget, alors on stop
        - create payment remainder,
        - si deposit :
            - create payment deposit
            - recalculate payment remainder

|> removePayments :
    -> role admin,
    -> système :
        - remove payment remainder
        - si deposit :
            - remove deposit

|> removeDeposit :
    -> role admin;
    -> systèm :
        - remove payment deposit
        - recalculate remainder (rate, amount, infos)

|> updateDeposit :
    -> role admin,
    -> systèm :
        - update deposit (rate, amount, infos)
        - recalculate remainder (rate, amount)

|> updateRamainder
    ->role admin,
    -> systèm :
        - update remainder (rate, amount, infos)
        - si deposit : 
            - recalculate deposit (rate, amount, infos)
