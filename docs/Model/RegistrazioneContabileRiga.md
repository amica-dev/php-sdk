# # RegistrazioneContabileRiga

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**registrazione_contabile_id** | **int** | Identificativo univoco di &#x60;RegistrazioneContabile&#x60;. | [optional]
**scadenza_id** | **int** | Identificativo univoco di &#x60;Scadenza&#x60;. | [optional]
**causale_contabile_id** | **int** | Identificativo univoco di &#x60;CausaleContabile&#x60;. | [optional]
**emissione_id** | **int** | Identificativo del &#x60;Documento&#x60; di riferimento. | [optional]
**tipo_conto** | **string** | Tipo di conto (piano dei conti). | [optional]
**sezione_conto** | **string** | Sezione del piano dei conti. | [optional]
**partita_anno** | **int** | Anno della partita aperta. | [optional]
**partita_numero** | **int** | Numero della partita aperta. | [optional]
**conto** | **string** | Numero di conto (ad es. \&quot;100.20\&quot;). | [optional]
**conto_raw** | **string** | Numero di conto, in formato grezzo (ad es. \&quot;  100   20\&quot;). | [optional]
**descrizione_conto** | **string** | Descrizione. | [optional]
**dare** | **float** | Importo in dare. | [optional]
**avere** | **float** | Importo in avere. | [optional]
**note** | **string** | Note. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
