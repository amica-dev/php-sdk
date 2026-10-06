# # Listino

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**fornitore_id** | **int** | Identificativo univoco di &#x60;Anagrafica&#x60; (fornitore). | [optional]
**fornitore** | [**\Amica\Sdk\Model\Anagrafica**](Anagrafica.md) |  | [optional]
**valuta_id** | **int** | Identificativo univoco di &#x60;Valuta&#x60; (valuta). | [optional]
**valuta** | [**\Amica\Sdk\Model\Valuta**](Valuta.md) |  | [optional]
**nome** | **string** | Nome del listino. |
**iva_inclusa** | **bool** | Indica se i prezzi base sono IVA inclusa oppure no. | [optional]
**ricarico** | **float** | Ricarico da applicare ai prezzi base di tutte le righe del listino. | [optional]
**data_creazione** | **\DateTime** | Data di creazione. | [optional]
**valido_dal** | **\DateTime** | Inizio validità del listino. | [optional]
**valido_al** | **\DateTime** | Fine validità del listino. | [optional]
**tipo_listino** | **string** | Tipo di listino. | [optional]
**calcolo_prezzi** | **string** | Modalità di calcolo dei prezzi del listino. | [optional]
**note** | **string** | Note. | [optional]
**righe** | [**\Amica\Sdk\Model\Prezzo[]**](Prezzo.md) | Righe del listino. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
