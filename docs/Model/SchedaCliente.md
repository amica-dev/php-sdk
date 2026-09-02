# # SchedaCliente

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**anagrafica_id** | **int** | Identificativo univoco di &#x60;Anagrafica&#x60;. | [optional]
**agente_id** | **int** | Identificativo univoco di &#x60;Anagrafica&#x60; dell&#39;agente. | [optional]
**agente** | [**\Amica\Sdk\Model\Anagrafica**](Anagrafica.md) |  | [optional]
**pagamento_id** | **int** | Identificativo univoco di &#x60;Pagamento&#x60;. | [optional]
**pagamento** | [**\Amica\Sdk\Model\Pagamento**](Pagamento.md) |  | [optional]
**listino_id** | **int** | Identificativo univoco di &#x60;Listino&#x60;. | [optional]
**listino** | [**\Amica\Sdk\Model\Listino**](Listino.md) |  | [optional]
**categoria_anagrafica_id** | **int** | Identificativo univoco di &#x60;CategoriaAnagrafica&#x60;. | [optional]
**categoria_anagrafica** | [**\Amica\Sdk\Model\CategoriaAnagrafica**](CategoriaAnagrafica.md) |  | [optional]
**causale_iva_id** | **int** | Identificativo univoco di &#x60;CausaleIva&#x60;. | [optional]
**causale_iva** | [**\Amica\Sdk\Model\CausaleIva**](CausaleIva.md) |  | [optional]
**fido** | **float** | Fido concesso al cliente. | [optional]
**sconto** | **float** | Sconto riservato al cliente (percentuale). | [optional]
**fatturato_anno_corrente** | **float** | Fatturato realizzato nell&#39;anno corrente. | [optional]
**numero_fatture_anno_corrente** | **int** | Numero di fatture emesse nell&#39;anno corrente. | [optional]
**insoluti_anno_corrente** | **float** | Importo insoluto nell&#39;anno corrente. | [optional]
**numero_insoluti_anno_corrente** | **int** | Numero di insoluti nell&#39;anno corrente. | [optional]
**fatturato_anno_precedente** | **float** | Fatturato realizzato nell&#39;anno precedente. | [optional]
**numero_fatture_anno_precedente** | **int** | Numero di fatture emesse nell&#39;anno precedente. | [optional]
**insoluti_anno_precedente** | **float** | Importo insoluto nell&#39;anno precedente. | [optional]
**numero_insoluti_anno_precedente** | **int** | Numero di insoluti verificatisi nell&#39;anno precedente. | [optional]
**data_ultima_fattura** | **\DateTime** | Data dell&#39;ultima fattura. | [optional]
**data_ultimo_pagamento** | **\DateTime** | Data dell&#39;ultimo pagamento. | [optional]
**data_ultimo_insoluto** | **\DateTime** | Data dell&#39;ultimo insoluto. | [optional]
**conto_raw** | **string** | Numero di conto, in formato grezzo (es. \&quot;  100   20\&quot;). Sono ammessi fino a cinque livelli. | [optional]
**conto** | **string** | Numero di conto, in formato leggibile (es. \&quot;100.20\&quot;). | [optional] [readonly]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
