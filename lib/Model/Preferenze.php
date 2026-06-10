<?php
/**
 * Preferenze
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Customers API
 *
 * La Customers API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare la Customers API registra la tua applicazione sull'**App Store di Amica**. Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  - **Authorization Code con PKCE** (consigliato per app che operano per conto di un utente Amica): l'utente autorizza la tua app tramite la pagina di consenso dell'identity provider; la tua app riceve un `code` da scambiare con un access token.    ```   GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256   POST {identity-provider}/connect/token        grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri>   ```  - **Client Credentials** (per integrazioni server-to-server, senza utente interattivo):    ```   POST {identity-provider}/connect/token   Content-Type: application/x-www-form-urlencoded    grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=read write documenti contabilita   ```    > **Nota:** il flusso Client Credentials è disponibile solo per le **app private**, non per quelle pubblicate sul marketplace.  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-------|-------------| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-----------|-------------| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |--------|-------------| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-----------|-------------| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |--------|-------------| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Alcune risposte `400` includono un campo `overridable` che indica se l'errore di validazione può essere ignorato impostando il parametro `validationOverride` a `true`.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: v3
 * Contact: assistenza@gestionaleamica.com
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.11.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Amica\Sdk\Model;

use \ArrayAccess;
use \Amica\Sdk\ObjectSerializer;

/**
 * Preferenze Class Doc Comment
 *
 * @category Class
 * @description Preferenze aziendali.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Preferenze implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Preferenze';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'ragione_sociale1' => 'string',
        'ragione_sociale2' => 'string',
        'partita_iva' => 'string',
        'registro_imprese' => 'string',
        'codice_fiscale' => 'string',
        'codice_sia' => 'string',
        'codice_rea' => 'string',
        'persona_giuridica' => 'bool',
        'data_nascita' => '\DateTime',
        'localita_nascita' => 'string',
        'provincia_nascita' => 'string',
        'sesso' => 'string',
        'attivita' => 'string',
        'iva_esclusa' => 'bool',
        'decimali_prezzo_unitario' => 'int',
        'causale_produzione_componenti_id' => 'int',
        'causale_produzione_composti_id' => 'int',
        'causale_magazzino_vendita_id' => 'int',
        'causale_magazzino_acquisto_id' => 'int',
        'ricerca_articolo' => '\Amica\Sdk\Model\RicercaArticolo',
        'data_speciale1' => 'string',
        'data_speciale2' => 'string',
        'giorni_extra_data_speciale1' => 'int',
        'giorni_extra_data_speciale2' => 'int',
        'pagato_manuale' => 'bool',
        'causale_magazzino_merci_id' => 'int',
        'magazzino_merci_id' => 'int',
        'listino_merci_id' => 'int',
        'modalita_pagamento_merci_id' => 'int',
        'documenti_causale_iva_id' => 'int',
        'documenti_magazzino_id' => 'int',
        'documenti_listino_id' => 'int',
        'e_mail' => 'string',
        'sito_web' => 'string',
        'anno_contabile' => 'int',
        'unit_di_misura' => 'string',
        'causale_pagamento_modello_cu' => 'string',
        'impostazioni_sdi' => '\Amica\Sdk\Model\ImpostazioniSdi',
        'tipo_bilancio' => 'string',
        'periodo_iva' => 'string',
        'liquidazione_iva' => 'string',
        'modifica_registrazione_contabile_alla_emissione' => 'bool',
        'ottimizza_emissione_ordini' => 'bool',
        'aggiungi_anno_in_corso_al_numero_documento' => 'bool',
        'consenti_cambio_manuale_stato_ddt' => 'bool',
        'email_body' => 'string',
        'email_auto_send' => 'bool',
        'auto_produzione' => 'bool',
        'codice_destinatario' => 'string',
        'pec' => 'string',
        'modello_vendite_nota_debito_id' => 'int',
        'modello_vendite_nota_credito_id' => 'int',
        'modello_vendite_ricevuta_fiscale_id' => 'int',
        'modello_vendite_fattura_professionista_id' => 'int',
        'modello_vendite_fattura_differita_id' => 'int',
        'modello_vendite_fattura_acconto_id' => 'int',
        'modello_acquisti_fattura_accompagnatoria_id' => 'int',
        'modello_vendite_fattura_accompagnaoria_id' => 'int',
        'modello_acquisti_nota_debito_id' => 'int',
        'modello_acquisti_nota_credito_id' => 'int',
        'modello_acquisti_fattura_professionisti_id' => 'int',
        'modello_acquisti_fattura_differita_id' => 'int',
        'modello_acquisti_fattura_acconto_id' => 'int',
        'sede_legale' => '\Amica\Sdk\Model\SedeLegale',
        'sede_operativa' => '\Amica\Sdk\Model\SedeOperativa',
        'sede_altra' => '\Amica\Sdk\Model\SedeAltra'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'id' => 'int32',
        'concurrency_token' => 'int32',
        'ragione_sociale1' => null,
        'ragione_sociale2' => null,
        'partita_iva' => null,
        'registro_imprese' => null,
        'codice_fiscale' => null,
        'codice_sia' => null,
        'codice_rea' => null,
        'persona_giuridica' => null,
        'data_nascita' => 'date-time',
        'localita_nascita' => null,
        'provincia_nascita' => null,
        'sesso' => null,
        'attivita' => null,
        'iva_esclusa' => null,
        'decimali_prezzo_unitario' => 'int32',
        'causale_produzione_componenti_id' => 'int32',
        'causale_produzione_composti_id' => 'int32',
        'causale_magazzino_vendita_id' => 'int32',
        'causale_magazzino_acquisto_id' => 'int32',
        'ricerca_articolo' => null,
        'data_speciale1' => null,
        'data_speciale2' => null,
        'giorni_extra_data_speciale1' => 'int32',
        'giorni_extra_data_speciale2' => 'int32',
        'pagato_manuale' => null,
        'causale_magazzino_merci_id' => 'int32',
        'magazzino_merci_id' => 'int32',
        'listino_merci_id' => 'int32',
        'modalita_pagamento_merci_id' => 'int32',
        'documenti_causale_iva_id' => 'int32',
        'documenti_magazzino_id' => 'int32',
        'documenti_listino_id' => 'int32',
        'e_mail' => null,
        'sito_web' => null,
        'anno_contabile' => 'int32',
        'unit_di_misura' => null,
        'causale_pagamento_modello_cu' => null,
        'impostazioni_sdi' => null,
        'tipo_bilancio' => null,
        'periodo_iva' => null,
        'liquidazione_iva' => null,
        'modifica_registrazione_contabile_alla_emissione' => null,
        'ottimizza_emissione_ordini' => null,
        'aggiungi_anno_in_corso_al_numero_documento' => null,
        'consenti_cambio_manuale_stato_ddt' => null,
        'email_body' => null,
        'email_auto_send' => null,
        'auto_produzione' => null,
        'codice_destinatario' => null,
        'pec' => null,
        'modello_vendite_nota_debito_id' => 'int32',
        'modello_vendite_nota_credito_id' => 'int32',
        'modello_vendite_ricevuta_fiscale_id' => 'int32',
        'modello_vendite_fattura_professionista_id' => 'int32',
        'modello_vendite_fattura_differita_id' => 'int32',
        'modello_vendite_fattura_acconto_id' => 'int32',
        'modello_acquisti_fattura_accompagnatoria_id' => 'int32',
        'modello_vendite_fattura_accompagnaoria_id' => 'int32',
        'modello_acquisti_nota_debito_id' => 'int32',
        'modello_acquisti_nota_credito_id' => 'int32',
        'modello_acquisti_fattura_professionisti_id' => 'int32',
        'modello_acquisti_fattura_differita_id' => 'int32',
        'modello_acquisti_fattura_acconto_id' => 'int32',
        'sede_legale' => null,
        'sede_operativa' => null,
        'sede_altra' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'ragione_sociale1' => true,
        'ragione_sociale2' => true,
        'partita_iva' => true,
        'registro_imprese' => true,
        'codice_fiscale' => true,
        'codice_sia' => true,
        'codice_rea' => true,
        'persona_giuridica' => false,
        'data_nascita' => true,
        'localita_nascita' => true,
        'provincia_nascita' => true,
        'sesso' => true,
        'attivita' => true,
        'iva_esclusa' => false,
        'decimali_prezzo_unitario' => false,
        'causale_produzione_componenti_id' => true,
        'causale_produzione_composti_id' => true,
        'causale_magazzino_vendita_id' => true,
        'causale_magazzino_acquisto_id' => true,
        'ricerca_articolo' => false,
        'data_speciale1' => true,
        'data_speciale2' => true,
        'giorni_extra_data_speciale1' => true,
        'giorni_extra_data_speciale2' => true,
        'pagato_manuale' => false,
        'causale_magazzino_merci_id' => true,
        'magazzino_merci_id' => true,
        'listino_merci_id' => true,
        'modalita_pagamento_merci_id' => true,
        'documenti_causale_iva_id' => true,
        'documenti_magazzino_id' => true,
        'documenti_listino_id' => true,
        'e_mail' => true,
        'sito_web' => true,
        'anno_contabile' => true,
        'unit_di_misura' => true,
        'causale_pagamento_modello_cu' => true,
        'impostazioni_sdi' => false,
        'tipo_bilancio' => false,
        'periodo_iva' => false,
        'liquidazione_iva' => false,
        'modifica_registrazione_contabile_alla_emissione' => false,
        'ottimizza_emissione_ordini' => false,
        'aggiungi_anno_in_corso_al_numero_documento' => false,
        'consenti_cambio_manuale_stato_ddt' => false,
        'email_body' => true,
        'email_auto_send' => false,
        'auto_produzione' => false,
        'codice_destinatario' => true,
        'pec' => true,
        'modello_vendite_nota_debito_id' => true,
        'modello_vendite_nota_credito_id' => true,
        'modello_vendite_ricevuta_fiscale_id' => true,
        'modello_vendite_fattura_professionista_id' => true,
        'modello_vendite_fattura_differita_id' => true,
        'modello_vendite_fattura_acconto_id' => true,
        'modello_acquisti_fattura_accompagnatoria_id' => true,
        'modello_vendite_fattura_accompagnaoria_id' => true,
        'modello_acquisti_nota_debito_id' => true,
        'modello_acquisti_nota_credito_id' => true,
        'modello_acquisti_fattura_professionisti_id' => true,
        'modello_acquisti_fattura_differita_id' => true,
        'modello_acquisti_fattura_acconto_id' => true,
        'sede_legale' => false,
        'sede_operativa' => false,
        'sede_altra' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'id',
        'concurrency_token' => 'concurrencyToken',
        'ragione_sociale1' => 'ragioneSociale1',
        'ragione_sociale2' => 'ragioneSociale2',
        'partita_iva' => 'partitaIva',
        'registro_imprese' => 'registroImprese',
        'codice_fiscale' => 'codiceFiscale',
        'codice_sia' => 'codiceSia',
        'codice_rea' => 'codiceRea',
        'persona_giuridica' => 'personaGiuridica',
        'data_nascita' => 'dataNascita',
        'localita_nascita' => 'localitaNascita',
        'provincia_nascita' => 'provinciaNascita',
        'sesso' => 'sesso',
        'attivita' => 'attivita',
        'iva_esclusa' => 'ivaEsclusa',
        'decimali_prezzo_unitario' => 'decimaliPrezzoUnitario',
        'causale_produzione_componenti_id' => 'causaleProduzioneComponentiId',
        'causale_produzione_composti_id' => 'causaleProduzioneCompostiId',
        'causale_magazzino_vendita_id' => 'causaleMagazzinoVenditaId',
        'causale_magazzino_acquisto_id' => 'causaleMagazzinoAcquistoId',
        'ricerca_articolo' => 'ricercaArticolo',
        'data_speciale1' => 'dataSpeciale1',
        'data_speciale2' => 'dataSpeciale2',
        'giorni_extra_data_speciale1' => 'giorniExtraDataSpeciale1',
        'giorni_extra_data_speciale2' => 'giorniExtraDataSpeciale2',
        'pagato_manuale' => 'pagatoManuale',
        'causale_magazzino_merci_id' => 'causaleMagazzinoMerciId',
        'magazzino_merci_id' => 'magazzinoMerciId',
        'listino_merci_id' => 'listinoMerciId',
        'modalita_pagamento_merci_id' => 'modalitaPagamentoMerciId',
        'documenti_causale_iva_id' => 'documentiCausaleIvaId',
        'documenti_magazzino_id' => 'documentiMagazzinoId',
        'documenti_listino_id' => 'documentiListinoId',
        'e_mail' => 'eMail',
        'sito_web' => 'sitoWeb',
        'anno_contabile' => 'annoContabile',
        'unit_di_misura' => 'unitàDiMisura',
        'causale_pagamento_modello_cu' => 'causalePagamentoModelloCU',
        'impostazioni_sdi' => 'impostazioniSdi',
        'tipo_bilancio' => 'tipoBilancio',
        'periodo_iva' => 'periodoIva',
        'liquidazione_iva' => 'liquidazioneIva',
        'modifica_registrazione_contabile_alla_emissione' => 'modificaRegistrazioneContabileAllaEmissione',
        'ottimizza_emissione_ordini' => 'ottimizzaEmissioneOrdini',
        'aggiungi_anno_in_corso_al_numero_documento' => 'aggiungiAnnoInCorsoAlNumeroDocumento',
        'consenti_cambio_manuale_stato_ddt' => 'consentiCambioManualeStatoDdt',
        'email_body' => 'emailBody',
        'email_auto_send' => 'emailAutoSend',
        'auto_produzione' => 'autoProduzione',
        'codice_destinatario' => 'codiceDestinatario',
        'pec' => 'pec',
        'modello_vendite_nota_debito_id' => 'modelloVenditeNotaDebitoId',
        'modello_vendite_nota_credito_id' => 'modelloVenditeNotaCreditoId',
        'modello_vendite_ricevuta_fiscale_id' => 'modelloVenditeRicevutaFiscaleId',
        'modello_vendite_fattura_professionista_id' => 'modelloVenditeFatturaProfessionistaId',
        'modello_vendite_fattura_differita_id' => 'modelloVenditeFatturaDifferitaId',
        'modello_vendite_fattura_acconto_id' => 'modelloVenditeFatturaAccontoId',
        'modello_acquisti_fattura_accompagnatoria_id' => 'modelloAcquistiFatturaAccompagnatoriaId',
        'modello_vendite_fattura_accompagnaoria_id' => 'modelloVenditeFatturaAccompagnaoriaId',
        'modello_acquisti_nota_debito_id' => 'modelloAcquistiNotaDebitoId',
        'modello_acquisti_nota_credito_id' => 'modelloAcquistiNotaCreditoId',
        'modello_acquisti_fattura_professionisti_id' => 'modelloAcquistiFatturaProfessionistiId',
        'modello_acquisti_fattura_differita_id' => 'modelloAcquistiFatturaDifferitaId',
        'modello_acquisti_fattura_acconto_id' => 'modelloAcquistiFatturaAccontoId',
        'sede_legale' => 'sedeLegale',
        'sede_operativa' => 'sedeOperativa',
        'sede_altra' => 'sedeAltra'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'ragione_sociale1' => 'setRagioneSociale1',
        'ragione_sociale2' => 'setRagioneSociale2',
        'partita_iva' => 'setPartitaIva',
        'registro_imprese' => 'setRegistroImprese',
        'codice_fiscale' => 'setCodiceFiscale',
        'codice_sia' => 'setCodiceSia',
        'codice_rea' => 'setCodiceRea',
        'persona_giuridica' => 'setPersonaGiuridica',
        'data_nascita' => 'setDataNascita',
        'localita_nascita' => 'setLocalitaNascita',
        'provincia_nascita' => 'setProvinciaNascita',
        'sesso' => 'setSesso',
        'attivita' => 'setAttivita',
        'iva_esclusa' => 'setIvaEsclusa',
        'decimali_prezzo_unitario' => 'setDecimaliPrezzoUnitario',
        'causale_produzione_componenti_id' => 'setCausaleProduzioneComponentiId',
        'causale_produzione_composti_id' => 'setCausaleProduzioneCompostiId',
        'causale_magazzino_vendita_id' => 'setCausaleMagazzinoVenditaId',
        'causale_magazzino_acquisto_id' => 'setCausaleMagazzinoAcquistoId',
        'ricerca_articolo' => 'setRicercaArticolo',
        'data_speciale1' => 'setDataSpeciale1',
        'data_speciale2' => 'setDataSpeciale2',
        'giorni_extra_data_speciale1' => 'setGiorniExtraDataSpeciale1',
        'giorni_extra_data_speciale2' => 'setGiorniExtraDataSpeciale2',
        'pagato_manuale' => 'setPagatoManuale',
        'causale_magazzino_merci_id' => 'setCausaleMagazzinoMerciId',
        'magazzino_merci_id' => 'setMagazzinoMerciId',
        'listino_merci_id' => 'setListinoMerciId',
        'modalita_pagamento_merci_id' => 'setModalitaPagamentoMerciId',
        'documenti_causale_iva_id' => 'setDocumentiCausaleIvaId',
        'documenti_magazzino_id' => 'setDocumentiMagazzinoId',
        'documenti_listino_id' => 'setDocumentiListinoId',
        'e_mail' => 'setEMail',
        'sito_web' => 'setSitoWeb',
        'anno_contabile' => 'setAnnoContabile',
        'unit_di_misura' => 'setUnitDiMisura',
        'causale_pagamento_modello_cu' => 'setCausalePagamentoModelloCu',
        'impostazioni_sdi' => 'setImpostazioniSdi',
        'tipo_bilancio' => 'setTipoBilancio',
        'periodo_iva' => 'setPeriodoIva',
        'liquidazione_iva' => 'setLiquidazioneIva',
        'modifica_registrazione_contabile_alla_emissione' => 'setModificaRegistrazioneContabileAllaEmissione',
        'ottimizza_emissione_ordini' => 'setOttimizzaEmissioneOrdini',
        'aggiungi_anno_in_corso_al_numero_documento' => 'setAggiungiAnnoInCorsoAlNumeroDocumento',
        'consenti_cambio_manuale_stato_ddt' => 'setConsentiCambioManualeStatoDdt',
        'email_body' => 'setEmailBody',
        'email_auto_send' => 'setEmailAutoSend',
        'auto_produzione' => 'setAutoProduzione',
        'codice_destinatario' => 'setCodiceDestinatario',
        'pec' => 'setPec',
        'modello_vendite_nota_debito_id' => 'setModelloVenditeNotaDebitoId',
        'modello_vendite_nota_credito_id' => 'setModelloVenditeNotaCreditoId',
        'modello_vendite_ricevuta_fiscale_id' => 'setModelloVenditeRicevutaFiscaleId',
        'modello_vendite_fattura_professionista_id' => 'setModelloVenditeFatturaProfessionistaId',
        'modello_vendite_fattura_differita_id' => 'setModelloVenditeFatturaDifferitaId',
        'modello_vendite_fattura_acconto_id' => 'setModelloVenditeFatturaAccontoId',
        'modello_acquisti_fattura_accompagnatoria_id' => 'setModelloAcquistiFatturaAccompagnatoriaId',
        'modello_vendite_fattura_accompagnaoria_id' => 'setModelloVenditeFatturaAccompagnaoriaId',
        'modello_acquisti_nota_debito_id' => 'setModelloAcquistiNotaDebitoId',
        'modello_acquisti_nota_credito_id' => 'setModelloAcquistiNotaCreditoId',
        'modello_acquisti_fattura_professionisti_id' => 'setModelloAcquistiFatturaProfessionistiId',
        'modello_acquisti_fattura_differita_id' => 'setModelloAcquistiFatturaDifferitaId',
        'modello_acquisti_fattura_acconto_id' => 'setModelloAcquistiFatturaAccontoId',
        'sede_legale' => 'setSedeLegale',
        'sede_operativa' => 'setSedeOperativa',
        'sede_altra' => 'setSedeAltra'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'ragione_sociale1' => 'getRagioneSociale1',
        'ragione_sociale2' => 'getRagioneSociale2',
        'partita_iva' => 'getPartitaIva',
        'registro_imprese' => 'getRegistroImprese',
        'codice_fiscale' => 'getCodiceFiscale',
        'codice_sia' => 'getCodiceSia',
        'codice_rea' => 'getCodiceRea',
        'persona_giuridica' => 'getPersonaGiuridica',
        'data_nascita' => 'getDataNascita',
        'localita_nascita' => 'getLocalitaNascita',
        'provincia_nascita' => 'getProvinciaNascita',
        'sesso' => 'getSesso',
        'attivita' => 'getAttivita',
        'iva_esclusa' => 'getIvaEsclusa',
        'decimali_prezzo_unitario' => 'getDecimaliPrezzoUnitario',
        'causale_produzione_componenti_id' => 'getCausaleProduzioneComponentiId',
        'causale_produzione_composti_id' => 'getCausaleProduzioneCompostiId',
        'causale_magazzino_vendita_id' => 'getCausaleMagazzinoVenditaId',
        'causale_magazzino_acquisto_id' => 'getCausaleMagazzinoAcquistoId',
        'ricerca_articolo' => 'getRicercaArticolo',
        'data_speciale1' => 'getDataSpeciale1',
        'data_speciale2' => 'getDataSpeciale2',
        'giorni_extra_data_speciale1' => 'getGiorniExtraDataSpeciale1',
        'giorni_extra_data_speciale2' => 'getGiorniExtraDataSpeciale2',
        'pagato_manuale' => 'getPagatoManuale',
        'causale_magazzino_merci_id' => 'getCausaleMagazzinoMerciId',
        'magazzino_merci_id' => 'getMagazzinoMerciId',
        'listino_merci_id' => 'getListinoMerciId',
        'modalita_pagamento_merci_id' => 'getModalitaPagamentoMerciId',
        'documenti_causale_iva_id' => 'getDocumentiCausaleIvaId',
        'documenti_magazzino_id' => 'getDocumentiMagazzinoId',
        'documenti_listino_id' => 'getDocumentiListinoId',
        'e_mail' => 'getEMail',
        'sito_web' => 'getSitoWeb',
        'anno_contabile' => 'getAnnoContabile',
        'unit_di_misura' => 'getUnitDiMisura',
        'causale_pagamento_modello_cu' => 'getCausalePagamentoModelloCu',
        'impostazioni_sdi' => 'getImpostazioniSdi',
        'tipo_bilancio' => 'getTipoBilancio',
        'periodo_iva' => 'getPeriodoIva',
        'liquidazione_iva' => 'getLiquidazioneIva',
        'modifica_registrazione_contabile_alla_emissione' => 'getModificaRegistrazioneContabileAllaEmissione',
        'ottimizza_emissione_ordini' => 'getOttimizzaEmissioneOrdini',
        'aggiungi_anno_in_corso_al_numero_documento' => 'getAggiungiAnnoInCorsoAlNumeroDocumento',
        'consenti_cambio_manuale_stato_ddt' => 'getConsentiCambioManualeStatoDdt',
        'email_body' => 'getEmailBody',
        'email_auto_send' => 'getEmailAutoSend',
        'auto_produzione' => 'getAutoProduzione',
        'codice_destinatario' => 'getCodiceDestinatario',
        'pec' => 'getPec',
        'modello_vendite_nota_debito_id' => 'getModelloVenditeNotaDebitoId',
        'modello_vendite_nota_credito_id' => 'getModelloVenditeNotaCreditoId',
        'modello_vendite_ricevuta_fiscale_id' => 'getModelloVenditeRicevutaFiscaleId',
        'modello_vendite_fattura_professionista_id' => 'getModelloVenditeFatturaProfessionistaId',
        'modello_vendite_fattura_differita_id' => 'getModelloVenditeFatturaDifferitaId',
        'modello_vendite_fattura_acconto_id' => 'getModelloVenditeFatturaAccontoId',
        'modello_acquisti_fattura_accompagnatoria_id' => 'getModelloAcquistiFatturaAccompagnatoriaId',
        'modello_vendite_fattura_accompagnaoria_id' => 'getModelloVenditeFatturaAccompagnaoriaId',
        'modello_acquisti_nota_debito_id' => 'getModelloAcquistiNotaDebitoId',
        'modello_acquisti_nota_credito_id' => 'getModelloAcquistiNotaCreditoId',
        'modello_acquisti_fattura_professionisti_id' => 'getModelloAcquistiFatturaProfessionistiId',
        'modello_acquisti_fattura_differita_id' => 'getModelloAcquistiFatturaDifferitaId',
        'modello_acquisti_fattura_acconto_id' => 'getModelloAcquistiFatturaAccontoId',
        'sede_legale' => 'getSedeLegale',
        'sede_operativa' => 'getSedeOperativa',
        'sede_altra' => 'getSedeAltra'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }

    public const TIPO_BILANCIO_ANALITICO = 'Analitico';
    public const TIPO_BILANCIO_SINTENTICO = 'Sintentico';
    public const PERIODO_IVA_MENSILE = 'Mensile';
    public const PERIODO_IVA_TRIMESTRALE = 'Trimestrale';
    public const LIQUIDAZIONE_IVA_COMPETENZA = 'Competenza';
    public const LIQUIDAZIONE_IVA_CASSA = 'Cassa';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoBilancioAllowableValues()
    {
        return [
            self::TIPO_BILANCIO_ANALITICO,
            self::TIPO_BILANCIO_SINTENTICO,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getPeriodoIvaAllowableValues()
    {
        return [
            self::PERIODO_IVA_MENSILE,
            self::PERIODO_IVA_TRIMESTRALE,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getLiquidazioneIvaAllowableValues()
    {
        return [
            self::LIQUIDAZIONE_IVA_COMPETENZA,
            self::LIQUIDAZIONE_IVA_CASSA,
        ];
    }

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[]|null $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(?array $data = null)
    {
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('concurrency_token', $data ?? [], null);
        $this->setIfExists('ragione_sociale1', $data ?? [], null);
        $this->setIfExists('ragione_sociale2', $data ?? [], null);
        $this->setIfExists('partita_iva', $data ?? [], null);
        $this->setIfExists('registro_imprese', $data ?? [], null);
        $this->setIfExists('codice_fiscale', $data ?? [], null);
        $this->setIfExists('codice_sia', $data ?? [], null);
        $this->setIfExists('codice_rea', $data ?? [], null);
        $this->setIfExists('persona_giuridica', $data ?? [], null);
        $this->setIfExists('data_nascita', $data ?? [], null);
        $this->setIfExists('localita_nascita', $data ?? [], null);
        $this->setIfExists('provincia_nascita', $data ?? [], null);
        $this->setIfExists('sesso', $data ?? [], null);
        $this->setIfExists('attivita', $data ?? [], null);
        $this->setIfExists('iva_esclusa', $data ?? [], null);
        $this->setIfExists('decimali_prezzo_unitario', $data ?? [], null);
        $this->setIfExists('causale_produzione_componenti_id', $data ?? [], null);
        $this->setIfExists('causale_produzione_composti_id', $data ?? [], null);
        $this->setIfExists('causale_magazzino_vendita_id', $data ?? [], null);
        $this->setIfExists('causale_magazzino_acquisto_id', $data ?? [], null);
        $this->setIfExists('ricerca_articolo', $data ?? [], null);
        $this->setIfExists('data_speciale1', $data ?? [], null);
        $this->setIfExists('data_speciale2', $data ?? [], null);
        $this->setIfExists('giorni_extra_data_speciale1', $data ?? [], null);
        $this->setIfExists('giorni_extra_data_speciale2', $data ?? [], null);
        $this->setIfExists('pagato_manuale', $data ?? [], null);
        $this->setIfExists('causale_magazzino_merci_id', $data ?? [], null);
        $this->setIfExists('magazzino_merci_id', $data ?? [], null);
        $this->setIfExists('listino_merci_id', $data ?? [], null);
        $this->setIfExists('modalita_pagamento_merci_id', $data ?? [], null);
        $this->setIfExists('documenti_causale_iva_id', $data ?? [], null);
        $this->setIfExists('documenti_magazzino_id', $data ?? [], null);
        $this->setIfExists('documenti_listino_id', $data ?? [], null);
        $this->setIfExists('e_mail', $data ?? [], null);
        $this->setIfExists('sito_web', $data ?? [], null);
        $this->setIfExists('anno_contabile', $data ?? [], null);
        $this->setIfExists('unit_di_misura', $data ?? [], null);
        $this->setIfExists('causale_pagamento_modello_cu', $data ?? [], null);
        $this->setIfExists('impostazioni_sdi', $data ?? [], null);
        $this->setIfExists('tipo_bilancio', $data ?? [], null);
        $this->setIfExists('periodo_iva', $data ?? [], null);
        $this->setIfExists('liquidazione_iva', $data ?? [], null);
        $this->setIfExists('modifica_registrazione_contabile_alla_emissione', $data ?? [], null);
        $this->setIfExists('ottimizza_emissione_ordini', $data ?? [], null);
        $this->setIfExists('aggiungi_anno_in_corso_al_numero_documento', $data ?? [], null);
        $this->setIfExists('consenti_cambio_manuale_stato_ddt', $data ?? [], null);
        $this->setIfExists('email_body', $data ?? [], null);
        $this->setIfExists('email_auto_send', $data ?? [], null);
        $this->setIfExists('auto_produzione', $data ?? [], null);
        $this->setIfExists('codice_destinatario', $data ?? [], null);
        $this->setIfExists('pec', $data ?? [], null);
        $this->setIfExists('modello_vendite_nota_debito_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_nota_credito_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_ricevuta_fiscale_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_fattura_professionista_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_fattura_differita_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_fattura_acconto_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_fattura_accompagnatoria_id', $data ?? [], null);
        $this->setIfExists('modello_vendite_fattura_accompagnaoria_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_nota_debito_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_nota_credito_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_fattura_professionisti_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_fattura_differita_id', $data ?? [], null);
        $this->setIfExists('modello_acquisti_fattura_acconto_id', $data ?? [], null);
        $this->setIfExists('sede_legale', $data ?? [], null);
        $this->setIfExists('sede_operativa', $data ?? [], null);
        $this->setIfExists('sede_altra', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if (!is_null($this->container['ragione_sociale1']) && (mb_strlen($this->container['ragione_sociale1']) > 60)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale1', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['ragione_sociale1']) && (mb_strlen($this->container['ragione_sociale1']) < 0)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['ragione_sociale2']) && (mb_strlen($this->container['ragione_sociale2']) > 60)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale2', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['ragione_sociale2']) && (mb_strlen($this->container['ragione_sociale2']) < 0)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['registro_imprese']) && (mb_strlen($this->container['registro_imprese']) > 15)) {
            $invalidProperties[] = "invalid value for 'registro_imprese', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['registro_imprese']) && (mb_strlen($this->container['registro_imprese']) < 0)) {
            $invalidProperties[] = "invalid value for 'registro_imprese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_sia']) && (mb_strlen($this->container['codice_sia']) > 10)) {
            $invalidProperties[] = "invalid value for 'codice_sia', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['codice_sia']) && (mb_strlen($this->container['codice_sia']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_sia', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_rea']) && (mb_strlen($this->container['codice_rea']) > 10)) {
            $invalidProperties[] = "invalid value for 'codice_rea', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['codice_rea']) && (mb_strlen($this->container['codice_rea']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_rea', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['localita_nascita']) && (mb_strlen($this->container['localita_nascita']) > 40)) {
            $invalidProperties[] = "invalid value for 'localita_nascita', the character length must be smaller than or equal to 40.";
        }

        if (!is_null($this->container['localita_nascita']) && (mb_strlen($this->container['localita_nascita']) < 0)) {
            $invalidProperties[] = "invalid value for 'localita_nascita', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['provincia_nascita']) && (mb_strlen($this->container['provincia_nascita']) > 3)) {
            $invalidProperties[] = "invalid value for 'provincia_nascita', the character length must be smaller than or equal to 3.";
        }

        if (!is_null($this->container['provincia_nascita']) && (mb_strlen($this->container['provincia_nascita']) < 0)) {
            $invalidProperties[] = "invalid value for 'provincia_nascita', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['attivita']) && (mb_strlen($this->container['attivita']) > 60)) {
            $invalidProperties[] = "invalid value for 'attivita', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['attivita']) && (mb_strlen($this->container['attivita']) < 0)) {
            $invalidProperties[] = "invalid value for 'attivita', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['decimali_prezzo_unitario']) && ($this->container['decimali_prezzo_unitario'] > 5)) {
            $invalidProperties[] = "invalid value for 'decimali_prezzo_unitario', must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['decimali_prezzo_unitario']) && ($this->container['decimali_prezzo_unitario'] < 1)) {
            $invalidProperties[] = "invalid value for 'decimali_prezzo_unitario', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['data_speciale1']) && (mb_strlen($this->container['data_speciale1']) > 5)) {
            $invalidProperties[] = "invalid value for 'data_speciale1', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['data_speciale1']) && (mb_strlen($this->container['data_speciale1']) < 0)) {
            $invalidProperties[] = "invalid value for 'data_speciale1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['data_speciale2']) && (mb_strlen($this->container['data_speciale2']) > 5)) {
            $invalidProperties[] = "invalid value for 'data_speciale2', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['data_speciale2']) && (mb_strlen($this->container['data_speciale2']) < 0)) {
            $invalidProperties[] = "invalid value for 'data_speciale2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['e_mail']) && (mb_strlen($this->container['e_mail']) > 50)) {
            $invalidProperties[] = "invalid value for 'e_mail', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['e_mail']) && (mb_strlen($this->container['e_mail']) < 0)) {
            $invalidProperties[] = "invalid value for 'e_mail', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['sito_web']) && (mb_strlen($this->container['sito_web']) > 50)) {
            $invalidProperties[] = "invalid value for 'sito_web', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['sito_web']) && (mb_strlen($this->container['sito_web']) < 0)) {
            $invalidProperties[] = "invalid value for 'sito_web', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['unit_di_misura']) && (mb_strlen($this->container['unit_di_misura']) > 15)) {
            $invalidProperties[] = "invalid value for 'unit_di_misura', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['unit_di_misura']) && (mb_strlen($this->container['unit_di_misura']) < 0)) {
            $invalidProperties[] = "invalid value for 'unit_di_misura', the character length must be bigger than or equal to 0.";
        }

        $allowedValues = $this->getTipoBilancioAllowableValues();
        if (!is_null($this->container['tipo_bilancio']) && !in_array($this->container['tipo_bilancio'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_bilancio', must be one of '%s'",
                $this->container['tipo_bilancio'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getPeriodoIvaAllowableValues();
        if (!is_null($this->container['periodo_iva']) && !in_array($this->container['periodo_iva'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'periodo_iva', must be one of '%s'",
                $this->container['periodo_iva'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getLiquidazioneIvaAllowableValues();
        if (!is_null($this->container['liquidazione_iva']) && !in_array($this->container['liquidazione_iva'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'liquidazione_iva', must be one of '%s'",
                $this->container['liquidazione_iva'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['email_body']) && (mb_strlen($this->container['email_body']) > 1024)) {
            $invalidProperties[] = "invalid value for 'email_body', the character length must be smaller than or equal to 1024.";
        }

        if (!is_null($this->container['email_body']) && (mb_strlen($this->container['email_body']) < 0)) {
            $invalidProperties[] = "invalid value for 'email_body', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_destinatario']) && (mb_strlen($this->container['codice_destinatario']) > 7)) {
            $invalidProperties[] = "invalid value for 'codice_destinatario', the character length must be smaller than or equal to 7.";
        }

        if (!is_null($this->container['codice_destinatario']) && (mb_strlen($this->container['codice_destinatario']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_destinatario', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['pec']) && (mb_strlen($this->container['pec']) > 120)) {
            $invalidProperties[] = "invalid value for 'pec', the character length must be smaller than or equal to 120.";
        }

        if (!is_null($this->container['pec']) && (mb_strlen($this->container['pec']) < 0)) {
            $invalidProperties[] = "invalid value for 'pec', the character length must be bigger than or equal to 0.";
        }

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int|null $id Identificativo univoco.
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets concurrency_token
     *
     * @return int|null
     */
    public function getConcurrencyToken()
    {
        return $this->container['concurrency_token'];
    }

    /**
     * Sets concurrency_token
     *
     * @param int|null $concurrency_token Il token di concorrenza implementa il controllo di concorrenza ottimistica: a ogni operazione di aggiornamento o cancellazione il valore del token sul database viene confrontato con quello sull'entità. Se i valori coincidono, l'operazione viene eseguita; se differiscono, si assume che un altro utente abbia effettuato una scrittura nel frattempo e l'aggiornamento viene rifiutato.
     *
     * @return self
     */
    public function setConcurrencyToken($concurrency_token)
    {
        if (is_null($concurrency_token)) {
            throw new \InvalidArgumentException('non-nullable concurrency_token cannot be null');
        }
        $this->container['concurrency_token'] = $concurrency_token;

        return $this;
    }

    /**
     * Gets ragione_sociale1
     *
     * @return string|null
     */
    public function getRagioneSociale1()
    {
        return $this->container['ragione_sociale1'];
    }

    /**
     * Sets ragione_sociale1
     *
     * @param string|null $ragione_sociale1 Ragione sociale.
     *
     * @return self
     */
    public function setRagioneSociale1($ragione_sociale1)
    {
        if (is_null($ragione_sociale1)) {
            array_push($this->openAPINullablesSetToNull, 'ragione_sociale1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('ragione_sociale1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($ragione_sociale1) && (mb_strlen($ragione_sociale1) > 60)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale1 when calling Preferenze., must be smaller than or equal to 60.');
        }
        if (!is_null($ragione_sociale1) && (mb_strlen($ragione_sociale1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale1 when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['ragione_sociale1'] = $ragione_sociale1;

        return $this;
    }

    /**
     * Gets ragione_sociale2
     *
     * @return string|null
     */
    public function getRagioneSociale2()
    {
        return $this->container['ragione_sociale2'];
    }

    /**
     * Sets ragione_sociale2
     *
     * @param string|null $ragione_sociale2 Eventuale seconda riga della ragione sociale. Utile soprattutto in fase di stampa, quando si desidera spezzare una ragione sociale molto lunga su due righe distinte.
     *
     * @return self
     */
    public function setRagioneSociale2($ragione_sociale2)
    {
        if (is_null($ragione_sociale2)) {
            array_push($this->openAPINullablesSetToNull, 'ragione_sociale2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('ragione_sociale2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($ragione_sociale2) && (mb_strlen($ragione_sociale2) > 60)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale2 when calling Preferenze., must be smaller than or equal to 60.');
        }
        if (!is_null($ragione_sociale2) && (mb_strlen($ragione_sociale2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale2 when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['ragione_sociale2'] = $ragione_sociale2;

        return $this;
    }

    /**
     * Gets partita_iva
     *
     * @return string|null
     */
    public function getPartitaIva()
    {
        return $this->container['partita_iva'];
    }

    /**
     * Sets partita_iva
     *
     * @param string|null $partita_iva Partita IVA.
     *
     * @return self
     */
    public function setPartitaIva($partita_iva)
    {
        if (is_null($partita_iva)) {
            array_push($this->openAPINullablesSetToNull, 'partita_iva');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('partita_iva', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['partita_iva'] = $partita_iva;

        return $this;
    }

    /**
     * Gets registro_imprese
     *
     * @return string|null
     */
    public function getRegistroImprese()
    {
        return $this->container['registro_imprese'];
    }

    /**
     * Sets registro_imprese
     *
     * @param string|null $registro_imprese Numero di iscrizione al Registro Imprese.
     *
     * @return self
     */
    public function setRegistroImprese($registro_imprese)
    {
        if (is_null($registro_imprese)) {
            array_push($this->openAPINullablesSetToNull, 'registro_imprese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('registro_imprese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($registro_imprese) && (mb_strlen($registro_imprese) > 15)) {
            throw new \InvalidArgumentException('invalid length for $registro_imprese when calling Preferenze., must be smaller than or equal to 15.');
        }
        if (!is_null($registro_imprese) && (mb_strlen($registro_imprese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $registro_imprese when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['registro_imprese'] = $registro_imprese;

        return $this;
    }

    /**
     * Gets codice_fiscale
     *
     * @return string|null
     */
    public function getCodiceFiscale()
    {
        return $this->container['codice_fiscale'];
    }

    /**
     * Sets codice_fiscale
     *
     * @param string|null $codice_fiscale Codice fiscale.
     *
     * @return self
     */
    public function setCodiceFiscale($codice_fiscale)
    {
        if (is_null($codice_fiscale)) {
            array_push($this->openAPINullablesSetToNull, 'codice_fiscale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_fiscale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['codice_fiscale'] = $codice_fiscale;

        return $this;
    }

    /**
     * Gets codice_sia
     *
     * @return string|null
     */
    public function getCodiceSia()
    {
        return $this->container['codice_sia'];
    }

    /**
     * Sets codice_sia
     *
     * @param string|null $codice_sia Codice SIA.
     *
     * @return self
     */
    public function setCodiceSia($codice_sia)
    {
        if (is_null($codice_sia)) {
            array_push($this->openAPINullablesSetToNull, 'codice_sia');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_sia', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_sia) && (mb_strlen($codice_sia) > 10)) {
            throw new \InvalidArgumentException('invalid length for $codice_sia when calling Preferenze., must be smaller than or equal to 10.');
        }
        if (!is_null($codice_sia) && (mb_strlen($codice_sia) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_sia when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['codice_sia'] = $codice_sia;

        return $this;
    }

    /**
     * Gets codice_rea
     *
     * @return string|null
     */
    public function getCodiceRea()
    {
        return $this->container['codice_rea'];
    }

    /**
     * Sets codice_rea
     *
     * @param string|null $codice_rea Codice REA.
     *
     * @return self
     */
    public function setCodiceRea($codice_rea)
    {
        if (is_null($codice_rea)) {
            array_push($this->openAPINullablesSetToNull, 'codice_rea');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_rea', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_rea) && (mb_strlen($codice_rea) > 10)) {
            throw new \InvalidArgumentException('invalid length for $codice_rea when calling Preferenze., must be smaller than or equal to 10.');
        }
        if (!is_null($codice_rea) && (mb_strlen($codice_rea) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_rea when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['codice_rea'] = $codice_rea;

        return $this;
    }

    /**
     * Gets persona_giuridica
     *
     * @return bool|null
     */
    public function getPersonaGiuridica()
    {
        return $this->container['persona_giuridica'];
    }

    /**
     * Sets persona_giuridica
     *
     * @param bool|null $persona_giuridica Indica se l'organizzazione è una persona giuridica oppure no. `true` indica una persona giuridica, `false` indica una persona fisica.
     *
     * @return self
     */
    public function setPersonaGiuridica($persona_giuridica)
    {
        if (is_null($persona_giuridica)) {
            throw new \InvalidArgumentException('non-nullable persona_giuridica cannot be null');
        }
        $this->container['persona_giuridica'] = $persona_giuridica;

        return $this;
    }

    /**
     * Gets data_nascita
     *
     * @return \DateTime|null
     */
    public function getDataNascita()
    {
        return $this->container['data_nascita'];
    }

    /**
     * Sets data_nascita
     *
     * @param \DateTime|null $data_nascita Data di nascita (persona fisica).
     *
     * @return self
     */
    public function setDataNascita($data_nascita)
    {
        if (is_null($data_nascita)) {
            array_push($this->openAPINullablesSetToNull, 'data_nascita');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_nascita', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_nascita'] = $data_nascita;

        return $this;
    }

    /**
     * Gets localita_nascita
     *
     * @return string|null
     */
    public function getLocalitaNascita()
    {
        return $this->container['localita_nascita'];
    }

    /**
     * Sets localita_nascita
     *
     * @param string|null $localita_nascita Luogo di nascita (persona fisica).
     *
     * @return self
     */
    public function setLocalitaNascita($localita_nascita)
    {
        if (is_null($localita_nascita)) {
            array_push($this->openAPINullablesSetToNull, 'localita_nascita');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('localita_nascita', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($localita_nascita) && (mb_strlen($localita_nascita) > 40)) {
            throw new \InvalidArgumentException('invalid length for $localita_nascita when calling Preferenze., must be smaller than or equal to 40.');
        }
        if (!is_null($localita_nascita) && (mb_strlen($localita_nascita) < 0)) {
            throw new \InvalidArgumentException('invalid length for $localita_nascita when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['localita_nascita'] = $localita_nascita;

        return $this;
    }

    /**
     * Gets provincia_nascita
     *
     * @return string|null
     */
    public function getProvinciaNascita()
    {
        return $this->container['provincia_nascita'];
    }

    /**
     * Sets provincia_nascita
     *
     * @param string|null $provincia_nascita Provincia di nascita (persona fisica).
     *
     * @return self
     */
    public function setProvinciaNascita($provincia_nascita)
    {
        if (is_null($provincia_nascita)) {
            array_push($this->openAPINullablesSetToNull, 'provincia_nascita');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provincia_nascita', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($provincia_nascita) && (mb_strlen($provincia_nascita) > 3)) {
            throw new \InvalidArgumentException('invalid length for $provincia_nascita when calling Preferenze., must be smaller than or equal to 3.');
        }
        if (!is_null($provincia_nascita) && (mb_strlen($provincia_nascita) < 0)) {
            throw new \InvalidArgumentException('invalid length for $provincia_nascita when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['provincia_nascita'] = $provincia_nascita;

        return $this;
    }

    /**
     * Gets sesso
     *
     * @return string|null
     */
    public function getSesso()
    {
        return $this->container['sesso'];
    }

    /**
     * Sets sesso
     *
     * @param string|null $sesso Sesso (persona fisica).
     *
     * @return self
     */
    public function setSesso($sesso)
    {
        if (is_null($sesso)) {
            array_push($this->openAPINullablesSetToNull, 'sesso');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sesso', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sesso'] = $sesso;

        return $this;
    }

    /**
     * Gets attivita
     *
     * @return string|null
     */
    public function getAttivita()
    {
        return $this->container['attivita'];
    }

    /**
     * Sets attivita
     *
     * @param string|null $attivita Tipo di attività svolta dall'azienda o dalla persona fisica.
     *
     * @return self
     */
    public function setAttivita($attivita)
    {
        if (is_null($attivita)) {
            array_push($this->openAPINullablesSetToNull, 'attivita');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('attivita', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($attivita) && (mb_strlen($attivita) > 60)) {
            throw new \InvalidArgumentException('invalid length for $attivita when calling Preferenze., must be smaller than or equal to 60.');
        }
        if (!is_null($attivita) && (mb_strlen($attivita) < 0)) {
            throw new \InvalidArgumentException('invalid length for $attivita when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['attivita'] = $attivita;

        return $this;
    }

    /**
     * Gets iva_esclusa
     *
     * @return bool|null
     */
    public function getIvaEsclusa()
    {
        return $this->container['iva_esclusa'];
    }

    /**
     * Sets iva_esclusa
     *
     * @param bool|null $iva_esclusa Indica se, in modo predefinito, i prezzi riportati sui documenti fiscali devono essere considerati al netto dell'IVA. Se `false`, l'IVA viene applicata ai prezzi quando possibile.
     *
     * @return self
     */
    public function setIvaEsclusa($iva_esclusa)
    {
        if (is_null($iva_esclusa)) {
            throw new \InvalidArgumentException('non-nullable iva_esclusa cannot be null');
        }
        $this->container['iva_esclusa'] = $iva_esclusa;

        return $this;
    }

    /**
     * Gets decimali_prezzo_unitario
     *
     * @return int|null
     */
    public function getDecimaliPrezzoUnitario()
    {
        return $this->container['decimali_prezzo_unitario'];
    }

    /**
     * Sets decimali_prezzo_unitario
     *
     * @param int|null $decimali_prezzo_unitario Numero di decimali ammessi nei prezzi unitari. Minimo 1, massimo 5.
     *
     * @return self
     */
    public function setDecimaliPrezzoUnitario($decimali_prezzo_unitario)
    {
        if (is_null($decimali_prezzo_unitario)) {
            throw new \InvalidArgumentException('non-nullable decimali_prezzo_unitario cannot be null');
        }

        if (($decimali_prezzo_unitario > 5)) {
            throw new \InvalidArgumentException('invalid value for $decimali_prezzo_unitario when calling Preferenze., must be smaller than or equal to 5.');
        }
        if (($decimali_prezzo_unitario < 1)) {
            throw new \InvalidArgumentException('invalid value for $decimali_prezzo_unitario when calling Preferenze., must be bigger than or equal to 1.');
        }

        $this->container['decimali_prezzo_unitario'] = $decimali_prezzo_unitario;

        return $this;
    }

    /**
     * Gets causale_produzione_componenti_id
     *
     * @return int|null
     */
    public function getCausaleProduzioneComponentiId()
    {
        return $this->container['causale_produzione_componenti_id'];
    }

    /**
     * Sets causale_produzione_componenti_id
     *
     * @param int|null $causale_produzione_componenti_id Identificativo univoco di Amica.CausaleMagazzino Identificativo univoco della Amica.CausaleMagazzino da usare nella produzione di componenti.
     *
     * @return self
     */
    public function setCausaleProduzioneComponentiId($causale_produzione_componenti_id)
    {
        if (is_null($causale_produzione_componenti_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_produzione_componenti_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_produzione_componenti_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_produzione_componenti_id'] = $causale_produzione_componenti_id;

        return $this;
    }

    /**
     * Gets causale_produzione_composti_id
     *
     * @return int|null
     */
    public function getCausaleProduzioneCompostiId()
    {
        return $this->container['causale_produzione_composti_id'];
    }

    /**
     * Sets causale_produzione_composti_id
     *
     * @param int|null $causale_produzione_composti_id Identificativo univoco della Amica.CausaleMagazzino da usare per gli articoli composti nella produzione di componenti.
     *
     * @return self
     */
    public function setCausaleProduzioneCompostiId($causale_produzione_composti_id)
    {
        if (is_null($causale_produzione_composti_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_produzione_composti_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_produzione_composti_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_produzione_composti_id'] = $causale_produzione_composti_id;

        return $this;
    }

    /**
     * Gets causale_magazzino_vendita_id
     *
     * @return int|null
     */
    public function getCausaleMagazzinoVenditaId()
    {
        return $this->container['causale_magazzino_vendita_id'];
    }

    /**
     * Sets causale_magazzino_vendita_id
     *
     * @param int|null $causale_magazzino_vendita_id Identificativo univoco della Amica.CausaleMagazzino da usare nei documenti di vendita.
     *
     * @return self
     */
    public function setCausaleMagazzinoVenditaId($causale_magazzino_vendita_id)
    {
        if (is_null($causale_magazzino_vendita_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_magazzino_vendita_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_magazzino_vendita_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_magazzino_vendita_id'] = $causale_magazzino_vendita_id;

        return $this;
    }

    /**
     * Gets causale_magazzino_acquisto_id
     *
     * @return int|null
     */
    public function getCausaleMagazzinoAcquistoId()
    {
        return $this->container['causale_magazzino_acquisto_id'];
    }

    /**
     * Sets causale_magazzino_acquisto_id
     *
     * @param int|null $causale_magazzino_acquisto_id Identificativo univoco della Amica.CausaleMagazzino da usare nei documenti di acquisto.
     *
     * @return self
     */
    public function setCausaleMagazzinoAcquistoId($causale_magazzino_acquisto_id)
    {
        if (is_null($causale_magazzino_acquisto_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_magazzino_acquisto_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_magazzino_acquisto_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_magazzino_acquisto_id'] = $causale_magazzino_acquisto_id;

        return $this;
    }

    /**
     * Gets ricerca_articolo
     *
     * @return \Amica\Sdk\Model\RicercaArticolo|null
     */
    public function getRicercaArticolo()
    {
        return $this->container['ricerca_articolo'];
    }

    /**
     * Sets ricerca_articolo
     *
     * @param \Amica\Sdk\Model\RicercaArticolo|null $ricerca_articolo ricerca_articolo
     *
     * @return self
     */
    public function setRicercaArticolo($ricerca_articolo)
    {
        if (is_null($ricerca_articolo)) {
            throw new \InvalidArgumentException('non-nullable ricerca_articolo cannot be null');
        }
        $this->container['ricerca_articolo'] = $ricerca_articolo;

        return $this;
    }

    /**
     * Gets data_speciale1
     *
     * @return string|null
     */
    public function getDataSpeciale1()
    {
        return $this->container['data_speciale1'];
    }

    /**
     * Sets data_speciale1
     *
     * @param string|null $data_speciale1 Eventuali scadenze che cadono in questa data verranno posticipate del numero di giorni indicato da Amica.Preferenze.GiorniExtraDataSpeciale1.
     *
     * @return self
     */
    public function setDataSpeciale1($data_speciale1)
    {
        if (is_null($data_speciale1)) {
            array_push($this->openAPINullablesSetToNull, 'data_speciale1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_speciale1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($data_speciale1) && (mb_strlen($data_speciale1) > 5)) {
            throw new \InvalidArgumentException('invalid length for $data_speciale1 when calling Preferenze., must be smaller than or equal to 5.');
        }
        if (!is_null($data_speciale1) && (mb_strlen($data_speciale1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $data_speciale1 when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['data_speciale1'] = $data_speciale1;

        return $this;
    }

    /**
     * Gets data_speciale2
     *
     * @return string|null
     */
    public function getDataSpeciale2()
    {
        return $this->container['data_speciale2'];
    }

    /**
     * Sets data_speciale2
     *
     * @param string|null $data_speciale2 Eventuali scadenze che cadono in questa data verranno posticipate del numero di giorni indicato da Amica.Preferenze.GiorniExtraDataSpeciale2.
     *
     * @return self
     */
    public function setDataSpeciale2($data_speciale2)
    {
        if (is_null($data_speciale2)) {
            array_push($this->openAPINullablesSetToNull, 'data_speciale2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_speciale2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($data_speciale2) && (mb_strlen($data_speciale2) > 5)) {
            throw new \InvalidArgumentException('invalid length for $data_speciale2 when calling Preferenze., must be smaller than or equal to 5.');
        }
        if (!is_null($data_speciale2) && (mb_strlen($data_speciale2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $data_speciale2 when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['data_speciale2'] = $data_speciale2;

        return $this;
    }

    /**
     * Gets giorni_extra_data_speciale1
     *
     * @return int|null
     */
    public function getGiorniExtraDataSpeciale1()
    {
        return $this->container['giorni_extra_data_speciale1'];
    }

    /**
     * Sets giorni_extra_data_speciale1
     *
     * @param int|null $giorni_extra_data_speciale1 Numero di giorni da aggiungere a Amica.Preferenze.DataSpeciale1.
     *
     * @return self
     */
    public function setGiorniExtraDataSpeciale1($giorni_extra_data_speciale1)
    {
        if (is_null($giorni_extra_data_speciale1)) {
            array_push($this->openAPINullablesSetToNull, 'giorni_extra_data_speciale1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('giorni_extra_data_speciale1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['giorni_extra_data_speciale1'] = $giorni_extra_data_speciale1;

        return $this;
    }

    /**
     * Gets giorni_extra_data_speciale2
     *
     * @return int|null
     */
    public function getGiorniExtraDataSpeciale2()
    {
        return $this->container['giorni_extra_data_speciale2'];
    }

    /**
     * Sets giorni_extra_data_speciale2
     *
     * @param int|null $giorni_extra_data_speciale2 Numero di giorni da aggiungere a Amica.Preferenze.DataSpeciale1.
     *
     * @return self
     */
    public function setGiorniExtraDataSpeciale2($giorni_extra_data_speciale2)
    {
        if (is_null($giorni_extra_data_speciale2)) {
            array_push($this->openAPINullablesSetToNull, 'giorni_extra_data_speciale2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('giorni_extra_data_speciale2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['giorni_extra_data_speciale2'] = $giorni_extra_data_speciale2;

        return $this;
    }

    /**
     * Gets pagato_manuale
     *
     * @return bool|null
     */
    public function getPagatoManuale()
    {
        return $this->container['pagato_manuale'];
    }

    /**
     * Sets pagato_manuale
     *
     * @param bool|null $pagato_manuale Indica se il pagamento delle scadenze viene gestito manualmente. Se `false`, lo stato del pagamento viene determinato in base alle registrazioni contabili (il modulo <a href=\"https://gestionaleamica.com/Software/ContabilitaOrdinaria\">Contabilità</a> deve essere attivo).
     *
     * @return self
     */
    public function setPagatoManuale($pagato_manuale)
    {
        if (is_null($pagato_manuale)) {
            throw new \InvalidArgumentException('non-nullable pagato_manuale cannot be null');
        }
        $this->container['pagato_manuale'] = $pagato_manuale;

        return $this;
    }

    /**
     * Gets causale_magazzino_merci_id
     *
     * @return int|null
     */
    public function getCausaleMagazzinoMerciId()
    {
        return $this->container['causale_magazzino_merci_id'];
    }

    /**
     * Sets causale_magazzino_merci_id
     *
     * @param int|null $causale_magazzino_merci_id Identificativo della Amica.CausaleMagazzino predefinita nei movimenti merci.
     *
     * @return self
     */
    public function setCausaleMagazzinoMerciId($causale_magazzino_merci_id)
    {
        if (is_null($causale_magazzino_merci_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_magazzino_merci_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_magazzino_merci_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_magazzino_merci_id'] = $causale_magazzino_merci_id;

        return $this;
    }

    /**
     * Gets magazzino_merci_id
     *
     * @return int|null
     */
    public function getMagazzinoMerciId()
    {
        return $this->container['magazzino_merci_id'];
    }

    /**
     * Sets magazzino_merci_id
     *
     * @param int|null $magazzino_merci_id Identificativo del Amica.Magazzino predefinito nei movimenti merci.
     *
     * @return self
     */
    public function setMagazzinoMerciId($magazzino_merci_id)
    {
        if (is_null($magazzino_merci_id)) {
            array_push($this->openAPINullablesSetToNull, 'magazzino_merci_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('magazzino_merci_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['magazzino_merci_id'] = $magazzino_merci_id;

        return $this;
    }

    /**
     * Gets listino_merci_id
     *
     * @return int|null
     */
    public function getListinoMerciId()
    {
        return $this->container['listino_merci_id'];
    }

    /**
     * Sets listino_merci_id
     *
     * @param int|null $listino_merci_id Identificativo del Amica.Listino predefinito nei movimenti merci.
     *
     * @return self
     */
    public function setListinoMerciId($listino_merci_id)
    {
        if (is_null($listino_merci_id)) {
            array_push($this->openAPINullablesSetToNull, 'listino_merci_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listino_merci_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listino_merci_id'] = $listino_merci_id;

        return $this;
    }

    /**
     * Gets modalita_pagamento_merci_id
     *
     * @return int|null
     */
    public function getModalitaPagamentoMerciId()
    {
        return $this->container['modalita_pagamento_merci_id'];
    }

    /**
     * Sets modalita_pagamento_merci_id
     *
     * @param int|null $modalita_pagamento_merci_id Identificativo della Amica.ModalitaPagamento predefinita nei movimenti merci.
     *
     * @return self
     */
    public function setModalitaPagamentoMerciId($modalita_pagamento_merci_id)
    {
        if (is_null($modalita_pagamento_merci_id)) {
            array_push($this->openAPINullablesSetToNull, 'modalita_pagamento_merci_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modalita_pagamento_merci_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modalita_pagamento_merci_id'] = $modalita_pagamento_merci_id;

        return $this;
    }

    /**
     * Gets documenti_causale_iva_id
     *
     * @return int|null
     */
    public function getDocumentiCausaleIvaId()
    {
        return $this->container['documenti_causale_iva_id'];
    }

    /**
     * Sets documenti_causale_iva_id
     *
     * @param int|null $documenti_causale_iva_id Identificativo della Amica.CausaleIva predefinita nei documenti fiscali.
     *
     * @return self
     */
    public function setDocumentiCausaleIvaId($documenti_causale_iva_id)
    {
        if (is_null($documenti_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'documenti_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documenti_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documenti_causale_iva_id'] = $documenti_causale_iva_id;

        return $this;
    }

    /**
     * Gets documenti_magazzino_id
     *
     * @return int|null
     */
    public function getDocumentiMagazzinoId()
    {
        return $this->container['documenti_magazzino_id'];
    }

    /**
     * Sets documenti_magazzino_id
     *
     * @param int|null $documenti_magazzino_id Identificativo del Amica.Magazzino predefinito nei documenti fiscali.
     *
     * @return self
     */
    public function setDocumentiMagazzinoId($documenti_magazzino_id)
    {
        if (is_null($documenti_magazzino_id)) {
            array_push($this->openAPINullablesSetToNull, 'documenti_magazzino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documenti_magazzino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documenti_magazzino_id'] = $documenti_magazzino_id;

        return $this;
    }

    /**
     * Gets documenti_listino_id
     *
     * @return int|null
     */
    public function getDocumentiListinoId()
    {
        return $this->container['documenti_listino_id'];
    }

    /**
     * Sets documenti_listino_id
     *
     * @param int|null $documenti_listino_id Amica.Preferenze.DocumentiListinoId predefinito nei documenti fiscali.
     *
     * @return self
     */
    public function setDocumentiListinoId($documenti_listino_id)
    {
        if (is_null($documenti_listino_id)) {
            array_push($this->openAPINullablesSetToNull, 'documenti_listino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documenti_listino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documenti_listino_id'] = $documenti_listino_id;

        return $this;
    }

    /**
     * Gets e_mail
     *
     * @return string|null
     */
    public function getEMail()
    {
        return $this->container['e_mail'];
    }

    /**
     * Sets e_mail
     *
     * @param string|null $e_mail Email aziendale.
     *
     * @return self
     */
    public function setEMail($e_mail)
    {
        if (is_null($e_mail)) {
            array_push($this->openAPINullablesSetToNull, 'e_mail');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('e_mail', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($e_mail) && (mb_strlen($e_mail) > 50)) {
            throw new \InvalidArgumentException('invalid length for $e_mail when calling Preferenze., must be smaller than or equal to 50.');
        }
        if (!is_null($e_mail) && (mb_strlen($e_mail) < 0)) {
            throw new \InvalidArgumentException('invalid length for $e_mail when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['e_mail'] = $e_mail;

        return $this;
    }

    /**
     * Gets sito_web
     *
     * @return string|null
     */
    public function getSitoWeb()
    {
        return $this->container['sito_web'];
    }

    /**
     * Sets sito_web
     *
     * @param string|null $sito_web Sito web aziendale.
     *
     * @return self
     */
    public function setSitoWeb($sito_web)
    {
        if (is_null($sito_web)) {
            array_push($this->openAPINullablesSetToNull, 'sito_web');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sito_web', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($sito_web) && (mb_strlen($sito_web) > 50)) {
            throw new \InvalidArgumentException('invalid length for $sito_web when calling Preferenze., must be smaller than or equal to 50.');
        }
        if (!is_null($sito_web) && (mb_strlen($sito_web) < 0)) {
            throw new \InvalidArgumentException('invalid length for $sito_web when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['sito_web'] = $sito_web;

        return $this;
    }

    /**
     * Gets anno_contabile
     *
     * @return int|null
     */
    public function getAnnoContabile()
    {
        return $this->container['anno_contabile'];
    }

    /**
     * Sets anno_contabile
     *
     * @param int|null $anno_contabile Anno contabile corrente (es. '2023').
     *
     * @return self
     */
    public function setAnnoContabile($anno_contabile)
    {
        if (is_null($anno_contabile)) {
            array_push($this->openAPINullablesSetToNull, 'anno_contabile');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('anno_contabile', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['anno_contabile'] = $anno_contabile;

        return $this;
    }

    /**
     * Gets unit_di_misura
     *
     * @return string|null
     */
    public function getUnitDiMisura()
    {
        return $this->container['unit_di_misura'];
    }

    /**
     * Sets unit_di_misura
     *
     * @param string|null $unit_di_misura Unità di misura predefinita.
     *
     * @return self
     */
    public function setUnitDiMisura($unit_di_misura)
    {
        if (is_null($unit_di_misura)) {
            array_push($this->openAPINullablesSetToNull, 'unit_di_misura');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_di_misura', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($unit_di_misura) && (mb_strlen($unit_di_misura) > 15)) {
            throw new \InvalidArgumentException('invalid length for $unit_di_misura when calling Preferenze., must be smaller than or equal to 15.');
        }
        if (!is_null($unit_di_misura) && (mb_strlen($unit_di_misura) < 0)) {
            throw new \InvalidArgumentException('invalid length for $unit_di_misura when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['unit_di_misura'] = $unit_di_misura;

        return $this;
    }

    /**
     * Gets causale_pagamento_modello_cu
     *
     * @return string|null
     */
    public function getCausalePagamentoModelloCu()
    {
        return $this->container['causale_pagamento_modello_cu'];
    }

    /**
     * Sets causale_pagamento_modello_cu
     *
     * @param string|null $causale_pagamento_modello_cu Causale del modello CU (es. 'A').
     *
     * @return self
     */
    public function setCausalePagamentoModelloCu($causale_pagamento_modello_cu)
    {
        if (is_null($causale_pagamento_modello_cu)) {
            array_push($this->openAPINullablesSetToNull, 'causale_pagamento_modello_cu');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_pagamento_modello_cu', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_pagamento_modello_cu'] = $causale_pagamento_modello_cu;

        return $this;
    }

    /**
     * Gets impostazioni_sdi
     *
     * @return \Amica\Sdk\Model\ImpostazioniSdi|null
     */
    public function getImpostazioniSdi()
    {
        return $this->container['impostazioni_sdi'];
    }

    /**
     * Sets impostazioni_sdi
     *
     * @param \Amica\Sdk\Model\ImpostazioniSdi|null $impostazioni_sdi impostazioni_sdi
     *
     * @return self
     */
    public function setImpostazioniSdi($impostazioni_sdi)
    {
        if (is_null($impostazioni_sdi)) {
            throw new \InvalidArgumentException('non-nullable impostazioni_sdi cannot be null');
        }
        $this->container['impostazioni_sdi'] = $impostazioni_sdi;

        return $this;
    }

    /**
     * Gets tipo_bilancio
     *
     * @return string|null
     */
    public function getTipoBilancio()
    {
        return $this->container['tipo_bilancio'];
    }

    /**
     * Sets tipo_bilancio
     *
     * @param string|null $tipo_bilancio Tipo di bilancio predefinito: analitico o sintetico.
     *
     * @return self
     */
    public function setTipoBilancio($tipo_bilancio)
    {
        if (is_null($tipo_bilancio)) {
            throw new \InvalidArgumentException('non-nullable tipo_bilancio cannot be null');
        }
        $allowedValues = $this->getTipoBilancioAllowableValues();
        if (!in_array($tipo_bilancio, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_bilancio', must be one of '%s'",
                    $tipo_bilancio,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_bilancio'] = $tipo_bilancio;

        return $this;
    }

    /**
     * Gets periodo_iva
     *
     * @return string|null
     */
    public function getPeriodoIva()
    {
        return $this->container['periodo_iva'];
    }

    /**
     * Sets periodo_iva
     *
     * @param string|null $periodo_iva Periodicità IVA: mensile o trimestrale.
     *
     * @return self
     */
    public function setPeriodoIva($periodo_iva)
    {
        if (is_null($periodo_iva)) {
            throw new \InvalidArgumentException('non-nullable periodo_iva cannot be null');
        }
        $allowedValues = $this->getPeriodoIvaAllowableValues();
        if (!in_array($periodo_iva, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'periodo_iva', must be one of '%s'",
                    $periodo_iva,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['periodo_iva'] = $periodo_iva;

        return $this;
    }

    /**
     * Gets liquidazione_iva
     *
     * @return string|null
     */
    public function getLiquidazioneIva()
    {
        return $this->container['liquidazione_iva'];
    }

    /**
     * Sets liquidazione_iva
     *
     * @param string|null $liquidazione_iva Tipo di liquidazione IVA: per competenza o per cassa.
     *
     * @return self
     */
    public function setLiquidazioneIva($liquidazione_iva)
    {
        if (is_null($liquidazione_iva)) {
            throw new \InvalidArgumentException('non-nullable liquidazione_iva cannot be null');
        }
        $allowedValues = $this->getLiquidazioneIvaAllowableValues();
        if (!in_array($liquidazione_iva, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'liquidazione_iva', must be one of '%s'",
                    $liquidazione_iva,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['liquidazione_iva'] = $liquidazione_iva;

        return $this;
    }

    /**
     * Gets modifica_registrazione_contabile_alla_emissione
     *
     * @return bool|null
     */
    public function getModificaRegistrazioneContabileAllaEmissione()
    {
        return $this->container['modifica_registrazione_contabile_alla_emissione'];
    }

    /**
     * Sets modifica_registrazione_contabile_alla_emissione
     *
     * @param bool|null $modifica_registrazione_contabile_alla_emissione Indica se si desidera modificare la registrazione contabile creata all'emissione di un documento. Si applica ad Amica20 WebApp e ad Amica 20 per Windows.
     *
     * @return self
     */
    public function setModificaRegistrazioneContabileAllaEmissione($modifica_registrazione_contabile_alla_emissione)
    {
        if (is_null($modifica_registrazione_contabile_alla_emissione)) {
            throw new \InvalidArgumentException('non-nullable modifica_registrazione_contabile_alla_emissione cannot be null');
        }
        $this->container['modifica_registrazione_contabile_alla_emissione'] = $modifica_registrazione_contabile_alla_emissione;

        return $this;
    }

    /**
     * Gets ottimizza_emissione_ordini
     *
     * @return bool|null
     */
    public function getOttimizzaEmissioneOrdini()
    {
        return $this->container['ottimizza_emissione_ordini'];
    }

    /**
     * Sets ottimizza_emissione_ordini
     *
     * @param bool|null $ottimizza_emissione_ordini Indica se l'ottimizzazione per l'emissione di documenti di grandi dimensioni è attiva oppure no. Si applica ad Amica20 per Windows.
     *
     * @return self
     */
    public function setOttimizzaEmissioneOrdini($ottimizza_emissione_ordini)
    {
        if (is_null($ottimizza_emissione_ordini)) {
            throw new \InvalidArgumentException('non-nullable ottimizza_emissione_ordini cannot be null');
        }
        $this->container['ottimizza_emissione_ordini'] = $ottimizza_emissione_ordini;

        return $this;
    }

    /**
     * Gets aggiungi_anno_in_corso_al_numero_documento
     *
     * @return bool|null
     */
    public function getAggiungiAnnoInCorsoAlNumeroDocumento()
    {
        return $this->container['aggiungi_anno_in_corso_al_numero_documento'];
    }

    /**
     * Sets aggiungi_anno_in_corso_al_numero_documento
     *
     * @param bool|null $aggiungi_anno_in_corso_al_numero_documento Indica se si desidera aggiungere l'anno in corso al numero del documento (es. 1/2022). Si applica ad Amica 20 WebApp e ad Amica 20 per Windows.
     *
     * @return self
     */
    public function setAggiungiAnnoInCorsoAlNumeroDocumento($aggiungi_anno_in_corso_al_numero_documento)
    {
        if (is_null($aggiungi_anno_in_corso_al_numero_documento)) {
            throw new \InvalidArgumentException('non-nullable aggiungi_anno_in_corso_al_numero_documento cannot be null');
        }
        $this->container['aggiungi_anno_in_corso_al_numero_documento'] = $aggiungi_anno_in_corso_al_numero_documento;

        return $this;
    }

    /**
     * Gets consenti_cambio_manuale_stato_ddt
     *
     * @return bool|null
     */
    public function getConsentiCambioManualeStatoDdt()
    {
        return $this->container['consenti_cambio_manuale_stato_ddt'];
    }

    /**
     * Sets consenti_cambio_manuale_stato_ddt
     *
     * @param bool|null $consenti_cambio_manuale_stato_ddt Indica se si desidera abilitare l'opzione di modifica dello stato dei DDT. Si applica ad Amica 20 WebApp e ad Amica 20 per Windows.
     *
     * @return self
     */
    public function setConsentiCambioManualeStatoDdt($consenti_cambio_manuale_stato_ddt)
    {
        if (is_null($consenti_cambio_manuale_stato_ddt)) {
            throw new \InvalidArgumentException('non-nullable consenti_cambio_manuale_stato_ddt cannot be null');
        }
        $this->container['consenti_cambio_manuale_stato_ddt'] = $consenti_cambio_manuale_stato_ddt;

        return $this;
    }

    /**
     * Gets email_body
     *
     * @return string|null
     */
    public function getEmailBody()
    {
        return $this->container['email_body'];
    }

    /**
     * Sets email_body
     *
     * @param string|null $email_body Testo del messaggio email inviato all'emissione di un documento. Il documento stesso viene allegato al messaggio. Si applica ad Amica 20 per Windows.
     *
     * @return self
     */
    public function setEmailBody($email_body)
    {
        if (is_null($email_body)) {
            array_push($this->openAPINullablesSetToNull, 'email_body');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('email_body', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($email_body) && (mb_strlen($email_body) > 1024)) {
            throw new \InvalidArgumentException('invalid length for $email_body when calling Preferenze., must be smaller than or equal to 1024.');
        }
        if (!is_null($email_body) && (mb_strlen($email_body) < 0)) {
            throw new \InvalidArgumentException('invalid length for $email_body when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['email_body'] = $email_body;

        return $this;
    }

    /**
     * Gets email_auto_send
     *
     * @return bool|null
     */
    public function getEmailAutoSend()
    {
        return $this->container['email_auto_send'];
    }

    /**
     * Sets email_auto_send
     *
     * @param bool|null $email_auto_send Indica se inviare un'email all'emissione di un documento.
     *
     * @return self
     */
    public function setEmailAutoSend($email_auto_send)
    {
        if (is_null($email_auto_send)) {
            throw new \InvalidArgumentException('non-nullable email_auto_send cannot be null');
        }
        $this->container['email_auto_send'] = $email_auto_send;

        return $this;
    }

    /**
     * Gets auto_produzione
     *
     * @return bool|null
     */
    public function getAutoProduzione()
    {
        return $this->container['auto_produzione'];
    }

    /**
     * Sets auto_produzione
     *
     * @param bool|null $auto_produzione Indica se la produzione automatica dei composti è attiva.
     *
     * @return self
     */
    public function setAutoProduzione($auto_produzione)
    {
        if (is_null($auto_produzione)) {
            throw new \InvalidArgumentException('non-nullable auto_produzione cannot be null');
        }
        $this->container['auto_produzione'] = $auto_produzione;

        return $this;
    }

    /**
     * Gets codice_destinatario
     *
     * @return string|null
     */
    public function getCodiceDestinatario()
    {
        return $this->container['codice_destinatario'];
    }

    /**
     * Sets codice_destinatario
     *
     * @param string|null $codice_destinatario 'Codice destinatario' per il sistema fiscale italiano. Utilizzato per il recapito delle fatture elettroniche italiane.
     *
     * @return self
     */
    public function setCodiceDestinatario($codice_destinatario)
    {
        if (is_null($codice_destinatario)) {
            array_push($this->openAPINullablesSetToNull, 'codice_destinatario');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_destinatario', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_destinatario) && (mb_strlen($codice_destinatario) > 7)) {
            throw new \InvalidArgumentException('invalid length for $codice_destinatario when calling Preferenze., must be smaller than or equal to 7.');
        }
        if (!is_null($codice_destinatario) && (mb_strlen($codice_destinatario) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_destinatario when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['codice_destinatario'] = $codice_destinatario;

        return $this;
    }

    /**
     * Gets pec
     *
     * @return string|null
     */
    public function getPec()
    {
        return $this->container['pec'];
    }

    /**
     * Sets pec
     *
     * @param string|null $pec Indirizzo PEC italiano.
     *
     * @return self
     */
    public function setPec($pec)
    {
        if (is_null($pec)) {
            array_push($this->openAPINullablesSetToNull, 'pec');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('pec', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($pec) && (mb_strlen($pec) > 120)) {
            throw new \InvalidArgumentException('invalid length for $pec when calling Preferenze., must be smaller than or equal to 120.');
        }
        if (!is_null($pec) && (mb_strlen($pec) < 0)) {
            throw new \InvalidArgumentException('invalid length for $pec when calling Preferenze., must be bigger than or equal to 0.');
        }

        $this->container['pec'] = $pec;

        return $this;
    }

    /**
     * Gets modello_vendite_nota_debito_id
     *
     * @return int|null
     */
    public function getModelloVenditeNotaDebitoId()
    {
        return $this->container['modello_vendite_nota_debito_id'];
    }

    /**
     * Sets modello_vendite_nota_debito_id
     *
     * @param int|null $modello_vendite_nota_debito_id Amica.ModelloContabile predefinito per le note di debito emesse.
     *
     * @return self
     */
    public function setModelloVenditeNotaDebitoId($modello_vendite_nota_debito_id)
    {
        if (is_null($modello_vendite_nota_debito_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_nota_debito_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_nota_debito_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_nota_debito_id'] = $modello_vendite_nota_debito_id;

        return $this;
    }

    /**
     * Gets modello_vendite_nota_credito_id
     *
     * @return int|null
     */
    public function getModelloVenditeNotaCreditoId()
    {
        return $this->container['modello_vendite_nota_credito_id'];
    }

    /**
     * Sets modello_vendite_nota_credito_id
     *
     * @param int|null $modello_vendite_nota_credito_id Amica.ModelloContabile predefinito per le note di credito emesse.
     *
     * @return self
     */
    public function setModelloVenditeNotaCreditoId($modello_vendite_nota_credito_id)
    {
        if (is_null($modello_vendite_nota_credito_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_nota_credito_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_nota_credito_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_nota_credito_id'] = $modello_vendite_nota_credito_id;

        return $this;
    }

    /**
     * Gets modello_vendite_ricevuta_fiscale_id
     *
     * @return int|null
     */
    public function getModelloVenditeRicevutaFiscaleId()
    {
        return $this->container['modello_vendite_ricevuta_fiscale_id'];
    }

    /**
     * Sets modello_vendite_ricevuta_fiscale_id
     *
     * @param int|null $modello_vendite_ricevuta_fiscale_id Amica.ModelloContabile predefinito per le ricevute fiscali emesse.
     *
     * @return self
     */
    public function setModelloVenditeRicevutaFiscaleId($modello_vendite_ricevuta_fiscale_id)
    {
        if (is_null($modello_vendite_ricevuta_fiscale_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_ricevuta_fiscale_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_ricevuta_fiscale_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_ricevuta_fiscale_id'] = $modello_vendite_ricevuta_fiscale_id;

        return $this;
    }

    /**
     * Gets modello_vendite_fattura_professionista_id
     *
     * @return int|null
     */
    public function getModelloVenditeFatturaProfessionistaId()
    {
        return $this->container['modello_vendite_fattura_professionista_id'];
    }

    /**
     * Sets modello_vendite_fattura_professionista_id
     *
     * @param int|null $modello_vendite_fattura_professionista_id Amica.ModelloContabile predefinito per le fatture professionisti emesse.
     *
     * @return self
     */
    public function setModelloVenditeFatturaProfessionistaId($modello_vendite_fattura_professionista_id)
    {
        if (is_null($modello_vendite_fattura_professionista_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_fattura_professionista_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_fattura_professionista_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_fattura_professionista_id'] = $modello_vendite_fattura_professionista_id;

        return $this;
    }

    /**
     * Gets modello_vendite_fattura_differita_id
     *
     * @return int|null
     */
    public function getModelloVenditeFatturaDifferitaId()
    {
        return $this->container['modello_vendite_fattura_differita_id'];
    }

    /**
     * Sets modello_vendite_fattura_differita_id
     *
     * @param int|null $modello_vendite_fattura_differita_id Amica.ModelloContabile predefinito per le fatture differite emesse.
     *
     * @return self
     */
    public function setModelloVenditeFatturaDifferitaId($modello_vendite_fattura_differita_id)
    {
        if (is_null($modello_vendite_fattura_differita_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_fattura_differita_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_fattura_differita_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_fattura_differita_id'] = $modello_vendite_fattura_differita_id;

        return $this;
    }

    /**
     * Gets modello_vendite_fattura_acconto_id
     *
     * @return int|null
     */
    public function getModelloVenditeFatturaAccontoId()
    {
        return $this->container['modello_vendite_fattura_acconto_id'];
    }

    /**
     * Sets modello_vendite_fattura_acconto_id
     *
     * @param int|null $modello_vendite_fattura_acconto_id Amica.ModelloContabile predefinito per le fatture di acconto emesse.
     *
     * @return self
     */
    public function setModelloVenditeFatturaAccontoId($modello_vendite_fattura_acconto_id)
    {
        if (is_null($modello_vendite_fattura_acconto_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_fattura_acconto_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_fattura_acconto_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_fattura_acconto_id'] = $modello_vendite_fattura_acconto_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_fattura_accompagnatoria_id
     *
     * @return int|null
     */
    public function getModelloAcquistiFatturaAccompagnatoriaId()
    {
        return $this->container['modello_acquisti_fattura_accompagnatoria_id'];
    }

    /**
     * Sets modello_acquisti_fattura_accompagnatoria_id
     *
     * @param int|null $modello_acquisti_fattura_accompagnatoria_id Amica.ModelloContabile predefinito per le fatture accompagnatorie fornitori emesse.
     *
     * @return self
     */
    public function setModelloAcquistiFatturaAccompagnatoriaId($modello_acquisti_fattura_accompagnatoria_id)
    {
        if (is_null($modello_acquisti_fattura_accompagnatoria_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_fattura_accompagnatoria_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_fattura_accompagnatoria_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_fattura_accompagnatoria_id'] = $modello_acquisti_fattura_accompagnatoria_id;

        return $this;
    }

    /**
     * Gets modello_vendite_fattura_accompagnaoria_id
     *
     * @return int|null
     */
    public function getModelloVenditeFatturaAccompagnaoriaId()
    {
        return $this->container['modello_vendite_fattura_accompagnaoria_id'];
    }

    /**
     * Sets modello_vendite_fattura_accompagnaoria_id
     *
     * @param int|null $modello_vendite_fattura_accompagnaoria_id Amica.ModelloContabile predefinito per le fatture accompagnatorie emesse.
     *
     * @return self
     */
    public function setModelloVenditeFatturaAccompagnaoriaId($modello_vendite_fattura_accompagnaoria_id)
    {
        if (is_null($modello_vendite_fattura_accompagnaoria_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_vendite_fattura_accompagnaoria_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_vendite_fattura_accompagnaoria_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_vendite_fattura_accompagnaoria_id'] = $modello_vendite_fattura_accompagnaoria_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_nota_debito_id
     *
     * @return int|null
     */
    public function getModelloAcquistiNotaDebitoId()
    {
        return $this->container['modello_acquisti_nota_debito_id'];
    }

    /**
     * Sets modello_acquisti_nota_debito_id
     *
     * @param int|null $modello_acquisti_nota_debito_id Amica.ModelloContabile predefinito per le note di debito fornitori emesse.
     *
     * @return self
     */
    public function setModelloAcquistiNotaDebitoId($modello_acquisti_nota_debito_id)
    {
        if (is_null($modello_acquisti_nota_debito_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_nota_debito_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_nota_debito_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_nota_debito_id'] = $modello_acquisti_nota_debito_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_nota_credito_id
     *
     * @return int|null
     */
    public function getModelloAcquistiNotaCreditoId()
    {
        return $this->container['modello_acquisti_nota_credito_id'];
    }

    /**
     * Sets modello_acquisti_nota_credito_id
     *
     * @param int|null $modello_acquisti_nota_credito_id Amica.ModelloContabile predefinito per le note di credito emesse.
     *
     * @return self
     */
    public function setModelloAcquistiNotaCreditoId($modello_acquisti_nota_credito_id)
    {
        if (is_null($modello_acquisti_nota_credito_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_nota_credito_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_nota_credito_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_nota_credito_id'] = $modello_acquisti_nota_credito_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_fattura_professionisti_id
     *
     * @return int|null
     */
    public function getModelloAcquistiFatturaProfessionistiId()
    {
        return $this->container['modello_acquisti_fattura_professionisti_id'];
    }

    /**
     * Sets modello_acquisti_fattura_professionisti_id
     *
     * @param int|null $modello_acquisti_fattura_professionisti_id Amica.ModelloContabile predefinito per le fatture professionisti fornitori emesse.
     *
     * @return self
     */
    public function setModelloAcquistiFatturaProfessionistiId($modello_acquisti_fattura_professionisti_id)
    {
        if (is_null($modello_acquisti_fattura_professionisti_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_fattura_professionisti_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_fattura_professionisti_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_fattura_professionisti_id'] = $modello_acquisti_fattura_professionisti_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_fattura_differita_id
     *
     * @return int|null
     */
    public function getModelloAcquistiFatturaDifferitaId()
    {
        return $this->container['modello_acquisti_fattura_differita_id'];
    }

    /**
     * Sets modello_acquisti_fattura_differita_id
     *
     * @param int|null $modello_acquisti_fattura_differita_id Amica.ModelloContabile predefinito per le fatture differite fornitori emesse.
     *
     * @return self
     */
    public function setModelloAcquistiFatturaDifferitaId($modello_acquisti_fattura_differita_id)
    {
        if (is_null($modello_acquisti_fattura_differita_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_fattura_differita_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_fattura_differita_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_fattura_differita_id'] = $modello_acquisti_fattura_differita_id;

        return $this;
    }

    /**
     * Gets modello_acquisti_fattura_acconto_id
     *
     * @return int|null
     */
    public function getModelloAcquistiFatturaAccontoId()
    {
        return $this->container['modello_acquisti_fattura_acconto_id'];
    }

    /**
     * Sets modello_acquisti_fattura_acconto_id
     *
     * @param int|null $modello_acquisti_fattura_acconto_id Amica.ModelloContabile predefinito per le fatture di acconto fornitori emesse.
     *
     * @return self
     */
    public function setModelloAcquistiFatturaAccontoId($modello_acquisti_fattura_acconto_id)
    {
        if (is_null($modello_acquisti_fattura_acconto_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_acquisti_fattura_acconto_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_acquisti_fattura_acconto_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_acquisti_fattura_acconto_id'] = $modello_acquisti_fattura_acconto_id;

        return $this;
    }

    /**
     * Gets sede_legale
     *
     * @return \Amica\Sdk\Model\SedeLegale|null
     */
    public function getSedeLegale()
    {
        return $this->container['sede_legale'];
    }

    /**
     * Sets sede_legale
     *
     * @param \Amica\Sdk\Model\SedeLegale|null $sede_legale sede_legale
     *
     * @return self
     */
    public function setSedeLegale($sede_legale)
    {
        if (is_null($sede_legale)) {
            throw new \InvalidArgumentException('non-nullable sede_legale cannot be null');
        }
        $this->container['sede_legale'] = $sede_legale;

        return $this;
    }

    /**
     * Gets sede_operativa
     *
     * @return \Amica\Sdk\Model\SedeOperativa|null
     */
    public function getSedeOperativa()
    {
        return $this->container['sede_operativa'];
    }

    /**
     * Sets sede_operativa
     *
     * @param \Amica\Sdk\Model\SedeOperativa|null $sede_operativa sede_operativa
     *
     * @return self
     */
    public function setSedeOperativa($sede_operativa)
    {
        if (is_null($sede_operativa)) {
            throw new \InvalidArgumentException('non-nullable sede_operativa cannot be null');
        }
        $this->container['sede_operativa'] = $sede_operativa;

        return $this;
    }

    /**
     * Gets sede_altra
     *
     * @return \Amica\Sdk\Model\SedeAltra|null
     */
    public function getSedeAltra()
    {
        return $this->container['sede_altra'];
    }

    /**
     * Sets sede_altra
     *
     * @param \Amica\Sdk\Model\SedeAltra|null $sede_altra sede_altra
     *
     * @return self
     */
    public function setSedeAltra($sede_altra)
    {
        if (is_null($sede_altra)) {
            throw new \InvalidArgumentException('non-nullable sede_altra cannot be null');
        }
        $this->container['sede_altra'] = $sede_altra;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


