# # Anagrafica

## Properties

Name | Type | Description | Notes
------------ | ------------- | ------------- | -------------
**id** | **int** | Identificativo univoco. | [optional]
**concurrency_token** | **int** | Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull&#39;entità. Se i valori coincidono, l&#39;operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l&#39;aggiornamento viene rifiutato. | [optional]
**modello_id** | **int** | Identificativo univoco di &#x60;Modello&#x60;. Sempre null. Questa proprietà è usata solo dal client desktop Amica 20 per Windows ed è da considerarsi obsoleta. | [optional]
**nazione_id** | **int** | Identificativo univoco di &#x60;Nazione&#x60; (nazione). | [optional]
**nazione** | [**\Amica\Sdk\Model\Nazione**](Nazione.md) |  | [optional]
**area_geografica_id** | **int** | Identificativo univoco di &#x60;AreaGeografica&#x60; (area geografica). | [optional]
**area_geografica** | [**\Amica\Sdk\Model\AreaGeografica**](AreaGeografica.md) |  | [optional]
**valuta_id** | **int** | Identificativo univoco di &#x60;Valuta&#x60; (valuta). | [optional]
**valuta** | [**\Amica\Sdk\Model\Valuta**](Valuta.md) |  | [optional]
**indirizzo_predefinito_id** | **int** | Identificativo univoco dell&#39;&#x60;Indirizzo&#x60; (indirizzo) predefinito. | [optional]
**indirizzi** | [**\Amica\Sdk\Model\Indirizzo[]**](Indirizzo.md) | Elenco degli &#x60;Indirizzo&#x60; dell&#39;anagrafica. | [optional]
**scheda_cliente** | [**\Amica\Sdk\Model\SchedaCliente**](SchedaCliente.md) |  | [optional]
**scheda_fornitore** | [**\Amica\Sdk\Model\SchedaFornitore**](SchedaFornitore.md) |  | [optional]
**stato** | **bool** | Indica se l&#39;anagrafica è attiva o meno. | [optional]
**cliente** | **bool** | Indica se l&#39;anagrafica è un cliente. | [optional]
**fornitore** | **bool** | Indica se l&#39;anagrafica è un fornitore. | [optional]
**agente** | **bool** | Indica se l&#39;anagrafica opera come agente di vendita per conto dell&#39;azienda. | [optional]
**vettore** | **bool** | Indica se l&#39;anagrafica opera come vettore. | [optional]
**capo_area** | **bool** | Indica se l&#39;anagrafica opera come capo area. | [optional]
**persona_giuridica** | **bool** | Indica se l&#39;anagrafica è una persona giuridica o meno. &#x60;true&#x60; indica una persona giuridica, &#x60;false&#x60; una persona fisica. | [optional]
**banca** | [**\Amica\Sdk\Model\Banca**](Banca.md) |  | [optional]
**nominativo1** | [**\Amica\Sdk\Model\AnagraficaNominativo1**](AnagraficaNominativo1.md) |  | [optional]
**nominativo2** | [**\Amica\Sdk\Model\AnagraficaNominativo2**](AnagraficaNominativo2.md) |  | [optional]
**sede_legale** | [**\Amica\Sdk\Model\AnagraficaSedeLegale**](AnagraficaSedeLegale.md) |  | [optional]
**codice** | **string** | Codice assegnato all&#39;anagrafica. | [optional]
**data_inserimento** | **\DateTime** | Data di primo inserimento. | [optional]
**ragione_sociale1** | **string** | Ragione sociale, oppure nome e cognome della persona fisica. |
**ragione_sociale2** | **string** | Eventuale seconda riga della ragione sociale. Usata principalmente nelle stampe, quando si vuole suddividere la ragione sociale su due righe. | [optional]
**e_mail** | **string** | Indirizzo email. | [optional]
**sito_web** | **string** | Indirizzo web. | [optional]
**partita_iva** | **string** | Partita IVA. La correttezza formale è verificata dal servizio al salvataggio: l&#39;errore che ne deriva è forzabile con &#x60;validationOverride&#x60;. | [optional]
**codice_fiscale** | **string** | Codice fiscale. Solitamente diverso dalla &#x60;PartitaIva&#x60;. Come per la partita IVA, la correttezza formale è verificata dal servizio al salvataggio e l&#39;errore è forzabile con &#x60;validationOverride&#x60;. | [optional]
**note** | **string** | Note. | [optional]
**note_importanti** | **string** | Note rilevanti. I client ufficiali Amica le evidenziano alla creazione di un nuovo documento fiscale. | [optional]
**pec** | **string** | Indirizzo Pec (posta elettronica certificata). | [optional]
**codice_destinatario** | **string** | Codice destinatario per il sistema fiscale italiano. Usato per il recapito delle fatture elettroniche italiane. | [optional]
**dichiarazione_intento** | **string** | Numero di protocollo della dichiarazione d&#39;intento. | [optional]
**riferimento_amministrazione** | **string** | Solitamente necessario quando si ha a che fare con enti della Pubblica Amministrazione italiana. Identifica univocamente l&#39;ufficio di destinazione all&#39;interno della PA. | [optional]

[[Back to Model list]](../../README.md#models) [[Back to API list]](../../README.md#endpoints) [[Back to README]](../../README.md)
