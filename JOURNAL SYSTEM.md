journal : libelle, date, debit|credit, equipement_id|budget_id|autre, mountant, caisse|banque|mobileMoney
        exemple: journal
        -> event_id (event_id)
        -> 2023-03-05 (date)
        -> achat de marchandise pour 20kg (wording)
        -> 123.000ar (amount)
        -> cash (payment_type)
        -> credit (flow) (bool)
        -> equipement (journalable_type)
            -> legume (journalable_id)

    calcul: caisse(box) => - 123000ar

tables:
- journal : id, date, event_id, equipement_id, flux(debit,credit), libellé, amount, quantity, payment_type|payment_id,
- bienMonetaire : id, id_event, account_id, amount, infos,
- account: id, name(caisse, banque, mobileMoney), infos

termes:
- flux (flow) : debit|credit
- caisse (cash), banque(bank), mobileMoney
- libellé(wording)
- journal (journal)