# # Pagamento

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**modalita_pagamento_id** | **int** | Identificativo univoco di Amica.Pagamento.ModalitaPagamento. | [optional]
**modalita_pagamento** | [**\Amica\Sdk\Model\ModalitaPagamento**](ModalitaPagamento.md) |  | [optional]
**spesa_id** | **int** | Identificativo univoco di Amica.Pagamento.Spesa. | [optional]
**spesa** | [**\Amica\Sdk\Model\Spesa**](Spesa.md) |  | [optional]
**risorsa_finanziaria_id** | **int** | Identificativo univoco di Amica.Pagamento.RisorsaFinanziaria. | [optional]
**risorsa_finanziaria** | [**\Amica\Sdk\Model\RisorsaFinanziaria**](RisorsaFinanziaria.md) |  | [optional]
**nome** | **string** | Nome, o descrizione. |
**calcolo_prima_rata** | **string** | Se e come l&#39;IVA e le spese devono essere considerate nel calcolo della prima rata. | [optional]
**decorrenza_prima_rata** | **string** | Da quando decorre il pagamento della prima rata. | [optional]
**giorni_aggiuntivi** | **int** | Giorni extra da aggiungere ad ogni scadenza. | [optional]
**sconto** | **float** | Sconto riconosciuto per l&#39;utilizzo del pagamento (percentuale). | [optional]
**inizio_scadenze** | **int** | Periodicità della prima rata. Per le successive, impostare Amica.Pagamento.Periodicità. | [optional]
**periodicit** | **int** | Periodicità delle rate successive alla prima. Per la prima rata, impostare Amica.Pagamento.InizioScadenze. Default impostato a 30. | [optional]
**fine_mese** | **bool** | Indica se le scadenze devono essere riportate a fine mese. | [optional]
**giorni_esatti** | **bool** | Indica se il pagamento è utilizzato nel caso di &#39;Art. 62 prodotti alimentari (giorni esatti)&#39;. | [optional]
**rate** | **int** | Numero di rate. | [optional]
**note** | **string** | Note. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
