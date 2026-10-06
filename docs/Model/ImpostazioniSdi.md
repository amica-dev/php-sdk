# # ImpostazioniSdi

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**api_key_sdi** | **string** | Chiave di licenza del servizio di interscambio fatture. | [optional]
**ultimo_download_sdi** | **\DateTime** | Data e ora dell&#39;ultimo aggiornamento ricevuto dal servizio di interscambio fatture (SdI). | [optional]
**includi_pdf_sdi** | **bool** | Indica se allegare il PDF della fattura a ciascun XML inviato al sistema di interscambio fatture (SdI). | [optional]
**abbina_articoli_sdi** | **bool** | Per le fatture di acquisto scaricate dal servizio di interscambio fatture (SdI), indica se si desidera tentare l&#39;abbinamento degli articoli/prodotti della fattura con quelli presenti nei magazzini aziendali. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
