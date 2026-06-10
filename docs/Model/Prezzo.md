# # Prezzo

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**listino_id** | **int** | Identificativo univoco di Amica.Listino. | [optional]
**articolo_id** | **int** | Identificativo univoco di Amica.Prezzo.Articolo. In alternativa, usare Amica.Prezzo.PrestazioneId. | [optional]
**articolo** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**prestazione_id** | **int** | Identificativo univoco di Amica.Prezzo.Prestazione. In alternativa, usare Amica.Prezzo.ArticoloId. | [optional]
**prestazione** | [**\Amica\Sdk\Model\Articolo**](Articolo.md) |  | [optional]
**importo** | **float** | Importo (prezzo). | [optional]
**sconto1** | **float** | Primo sconto percentuale, applicato sul prezzo del bene o della prestazione. | [optional]
**sconto2** | **float** | Secondo sconto percentuale, applicato a seguito dell&#39;applicazione di Amica.Prezzo.Sconto1 | [optional]
**sconto3** | **float** | Terzo sconto percentuale, applicato a seguito dell&#39;applicazione di Amica.Prezzo.Sconto2 | [optional]
**sconto4** | **float** | Quarto sconto percentuale, applicato a seguito dell&#39;applicazione di Amica.Prezzo.Sconto3 | [optional]
**ricarico** | **float** | Ricarico (percentuale) da applicare sul costo del prodotto (o della prestazione). Se utilizzato, Amica.Prezzo.Importo viene ignorato. | [optional]
**provvigione_agente** | **float** | Provvigione corrisposta all&#39;agente, in percentuale. | [optional]
**provvigione_capo_area** | **float** | Provvigione corrisposta al capo area, in percentuale. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
