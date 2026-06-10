# # MovimentoMerce

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**modalita_pagamento_id** | **int** | Identificativo univoco di Amica.MovimentoMerce.ModalitaPagamento. | [optional]
**modalita_pagamento** | [**\Amica\Sdk\Model\ModalitaPagamento**](ModalitaPagamento.md) |  | [optional]
**causale_magazzino_id** | **int** | Identificativo univoco di Amica.MovimentoMerce.CausaleMagazzino. | [optional]
**causale_magazzino** | [**\Amica\Sdk\Model\CausaleMagazzino**](CausaleMagazzino.md) |  | [optional]
**anagrafica_id** | **int** | Identificativo univoco di Amica.MovimentoMerce.Anagrafica. | [optional]
**anagrafica** | [**\Amica\Sdk\Model\Anagrafica**](Anagrafica.md) |  | [optional]
**documento_id** | **int** | Identificativo univoco di Amica.Documento. | [optional]
**iva_esclusa** | **bool** | Indica se i prezzi inseriti sono da considerarsi al netto dell&#39;IVA. Se &#x60;false&#x60;, l&#39;IVA verrà scorporata in fase di elaborazione (app ufficiali). | [optional]
**data** | **\DateTime** | Data del movimento. | [optional]
**descrizione** | **string** | Descrizione. | [optional]
**documento_numero** | **string** | Numero del documento di riferimento. | [optional]
**documento_data** | **\DateTime** | Data del documento di riferimento. | [optional]
**note** | **string** | Note. | [optional]
**righe** | [**\Amica\Sdk\Model\MovimentoMerceRiga[]**](MovimentoMerceRiga.md) | Righe del movimento. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
