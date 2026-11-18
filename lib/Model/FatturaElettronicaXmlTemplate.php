<?php
/**
 * FatturaElettronicaXmlTemplate
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Amica Cloud API
 *
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'[**App Store di Amica**](https://apps.amica20.it). Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  La scelta dipende da **come** la tua applicazione si autentica:  - C'è un **utente** che può accedere via browser (web app, SPA, app mobile)? → **Authorization Code con PKCE**. - È un **servizio headless** (backend, batch, integrazione server-to-server) di una **singola azienda**, senza un utente che effettua il login? → **Client Credentials**.  #### Authorization Code con PKCE  Consigliato per le app che operano per conto di un utente Amica ed è l'**unico flusso disponibile per le app pubblicate sul marketplace**. Una sola registrazione serve un numero qualsiasi di aziende: ogni utente autorizza la tua app per la propria azienda tramite la pagina di consenso dell'identity provider, e il token riceve automaticamente il contesto di quell'azienda. La tua app riceve un `code` da scambiare con un access token.  ``` GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256 POST {identity-provider}/connect/token      grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri> ```  #### Client Credentials  Per integrazioni **server-to-server** senza utente interattivo. Disponibile solo per le **app private**, non per quelle pubblicate sul marketplace. Un'app Client Credentials è **legata a una singola azienda** — quella dell'utente che la registra — e il token ne riceve automaticamente il contesto. Per servire un'azienda diversa serve una registrazione separata; se devi distribuire la stessa app a più aziende usa invece **Authorization Code**.  ``` POST {identity-provider}/connect/token Content-Type: application/x-www-form-urlencoded  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=comune read write documenti ```  > **Importante:** non includere lo scope `openid` (né `profile`/`email`) in questo flusso. `openid` attiva OpenID Connect — richiede un utente che effettua il login e l'emissione di un ID token — cosa che un flusso server-to-server non prevede: la richiesta viene rifiutata con l'errore `Client cannot request OpenID scopes in client credentials flow`. Indica solo gli scope di risorsa (`comune`, `magazzino`, `documenti`, `contabilita`, ed eventualmente `read`/`write`).  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — accesso negato (vedi sotto le possibili cause). | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Un `403` può avere tre cause; nelle ultime due il campo `code` della risposta indica quale:  - il token non possiede lo scope richiesto dall'endpoint; - `code: cloud_not_active` — l'account lavora con una connessione locale e la Cloud API non espone i suoi dati. Per attivare il servizio cloud contatta l'assistenza, poi richiedi un nuovo token: lo stato del servizio è letto al rilascio del token; - `code: company_license_not_active` — la licenza dell'azienda indicata nel percorso è scaduta o non è attiva. Le altre aziende con licenza attiva restano accessibili.  Il motivo dell'errore è sempre nel campo `detail`. Ogni risposta di errore riporta inoltre `overridable`: quando vale `true`, l'errore di validazione è *forzabile* e basta ripetere la stessa richiesta aggiungendo `validationOverride=true` alla query string perché il servizio la accetti. È il caso delle anomalie formali su dati reali — la partita IVA di un fornitore estero, per esempio — che il cliente ha comunque bisogno di registrare.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: 3.3.5
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
 * FatturaElettronicaXmlTemplate Class Doc Comment
 *
 * @category Class
 * @description Modello standard di fattura elettronica.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class FatturaElettronicaXmlTemplate implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'FatturaElettronicaXmlTemplate';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'soggetto_emittente' => 'string',
        'id_trasmittente_id_paese' => 'string',
        'cedente_prestatore_id_paese' => 'string',
        'progressivo_invio' => 'string',
        'albo_professionale' => 'string',
        'provincia_albo' => 'string',
        'numero_iscrizione_albo' => 'string',
        'data_iscrizione_albo' => '\DateTime',
        'regime_fiscale' => 'string',
        'sede_nazione' => 'string',
        'sede_numero_civico' => 'string',
        'stabile_organizzazione_indirizzo' => 'string',
        'stabile_organizzazione_numero_civico' => 'string',
        'stabile_organizzazione_cap' => 'string',
        'stabile_organizzazione_comune' => 'string',
        'stabile_organizzazione_provincia' => 'string',
        'stabile_organizzazione_nazione' => 'string',
        'rea_numero' => 'string',
        'rea_ufficio' => 'string',
        'rea_capitale_sociale' => 'float',
        'rea_socio_unico' => 'string',
        'rea_stato_liquidazione' => 'string',
        'rappresentante_id_paese' => 'string',
        'rappresentante_id_codice' => 'string',
        'rappresentante_codice_fiscale' => 'string',
        'rappresentante_denominazione' => 'string',
        'rappresentante_nome' => 'string',
        'rappresentante_cognome' => 'string',
        'rappresentante_titolo' => 'string',
        'rappresentante_cod_eori' => 'string',
        'intermediario_id_paese' => 'string',
        'intermediario_id_codice' => 'string',
        'intermediario_codice_fiscale' => 'string',
        'intermediario_denominazione' => 'string',
        'intermediario_nome' => 'string',
        'intermediario_cognome' => 'string',
        'intermediario_titolo' => 'string',
        'intermediario_cod_eori' => 'string'
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
        'soggetto_emittente' => null,
        'id_trasmittente_id_paese' => null,
        'cedente_prestatore_id_paese' => null,
        'progressivo_invio' => null,
        'albo_professionale' => null,
        'provincia_albo' => null,
        'numero_iscrizione_albo' => null,
        'data_iscrizione_albo' => 'date-time',
        'regime_fiscale' => null,
        'sede_nazione' => null,
        'sede_numero_civico' => null,
        'stabile_organizzazione_indirizzo' => null,
        'stabile_organizzazione_numero_civico' => null,
        'stabile_organizzazione_cap' => null,
        'stabile_organizzazione_comune' => null,
        'stabile_organizzazione_provincia' => null,
        'stabile_organizzazione_nazione' => null,
        'rea_numero' => null,
        'rea_ufficio' => null,
        'rea_capitale_sociale' => 'double',
        'rea_socio_unico' => null,
        'rea_stato_liquidazione' => null,
        'rappresentante_id_paese' => null,
        'rappresentante_id_codice' => null,
        'rappresentante_codice_fiscale' => null,
        'rappresentante_denominazione' => null,
        'rappresentante_nome' => null,
        'rappresentante_cognome' => null,
        'rappresentante_titolo' => null,
        'rappresentante_cod_eori' => null,
        'intermediario_id_paese' => null,
        'intermediario_id_codice' => null,
        'intermediario_codice_fiscale' => null,
        'intermediario_denominazione' => null,
        'intermediario_nome' => null,
        'intermediario_cognome' => null,
        'intermediario_titolo' => null,
        'intermediario_cod_eori' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'soggetto_emittente' => true,
        'id_trasmittente_id_paese' => true,
        'cedente_prestatore_id_paese' => true,
        'progressivo_invio' => true,
        'albo_professionale' => true,
        'provincia_albo' => true,
        'numero_iscrizione_albo' => true,
        'data_iscrizione_albo' => true,
        'regime_fiscale' => true,
        'sede_nazione' => true,
        'sede_numero_civico' => true,
        'stabile_organizzazione_indirizzo' => true,
        'stabile_organizzazione_numero_civico' => true,
        'stabile_organizzazione_cap' => true,
        'stabile_organizzazione_comune' => true,
        'stabile_organizzazione_provincia' => true,
        'stabile_organizzazione_nazione' => true,
        'rea_numero' => true,
        'rea_ufficio' => true,
        'rea_capitale_sociale' => true,
        'rea_socio_unico' => true,
        'rea_stato_liquidazione' => true,
        'rappresentante_id_paese' => true,
        'rappresentante_id_codice' => true,
        'rappresentante_codice_fiscale' => true,
        'rappresentante_denominazione' => true,
        'rappresentante_nome' => true,
        'rappresentante_cognome' => true,
        'rappresentante_titolo' => true,
        'rappresentante_cod_eori' => true,
        'intermediario_id_paese' => true,
        'intermediario_id_codice' => true,
        'intermediario_codice_fiscale' => true,
        'intermediario_denominazione' => true,
        'intermediario_nome' => true,
        'intermediario_cognome' => true,
        'intermediario_titolo' => true,
        'intermediario_cod_eori' => true
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
        'soggetto_emittente' => 'soggettoEmittente',
        'id_trasmittente_id_paese' => 'idTrasmittenteIdPaese',
        'cedente_prestatore_id_paese' => 'cedentePrestatoreIdPaese',
        'progressivo_invio' => 'progressivoInvio',
        'albo_professionale' => 'alboProfessionale',
        'provincia_albo' => 'provinciaAlbo',
        'numero_iscrizione_albo' => 'numeroIscrizioneAlbo',
        'data_iscrizione_albo' => 'dataIscrizioneAlbo',
        'regime_fiscale' => 'regimeFiscale',
        'sede_nazione' => 'sedeNazione',
        'sede_numero_civico' => 'sedeNumeroCivico',
        'stabile_organizzazione_indirizzo' => 'stabileOrganizzazioneIndirizzo',
        'stabile_organizzazione_numero_civico' => 'stabileOrganizzazioneNumeroCivico',
        'stabile_organizzazione_cap' => 'stabileOrganizzazioneCap',
        'stabile_organizzazione_comune' => 'stabileOrganizzazioneComune',
        'stabile_organizzazione_provincia' => 'stabileOrganizzazioneProvincia',
        'stabile_organizzazione_nazione' => 'stabileOrganizzazioneNazione',
        'rea_numero' => 'reaNumero',
        'rea_ufficio' => 'reaUfficio',
        'rea_capitale_sociale' => 'reaCapitaleSociale',
        'rea_socio_unico' => 'reaSocioUnico',
        'rea_stato_liquidazione' => 'reaStatoLiquidazione',
        'rappresentante_id_paese' => 'rappresentanteIdPaese',
        'rappresentante_id_codice' => 'rappresentanteIdCodice',
        'rappresentante_codice_fiscale' => 'rappresentanteCodiceFiscale',
        'rappresentante_denominazione' => 'rappresentanteDenominazione',
        'rappresentante_nome' => 'rappresentanteNome',
        'rappresentante_cognome' => 'rappresentanteCognome',
        'rappresentante_titolo' => 'rappresentanteTitolo',
        'rappresentante_cod_eori' => 'rappresentanteCodEori',
        'intermediario_id_paese' => 'intermediarioIdPaese',
        'intermediario_id_codice' => 'intermediarioIdCodice',
        'intermediario_codice_fiscale' => 'intermediarioCodiceFiscale',
        'intermediario_denominazione' => 'intermediarioDenominazione',
        'intermediario_nome' => 'intermediarioNome',
        'intermediario_cognome' => 'intermediarioCognome',
        'intermediario_titolo' => 'intermediarioTitolo',
        'intermediario_cod_eori' => 'intermediarioCodEori'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'soggetto_emittente' => 'setSoggettoEmittente',
        'id_trasmittente_id_paese' => 'setIdTrasmittenteIdPaese',
        'cedente_prestatore_id_paese' => 'setCedentePrestatoreIdPaese',
        'progressivo_invio' => 'setProgressivoInvio',
        'albo_professionale' => 'setAlboProfessionale',
        'provincia_albo' => 'setProvinciaAlbo',
        'numero_iscrizione_albo' => 'setNumeroIscrizioneAlbo',
        'data_iscrizione_albo' => 'setDataIscrizioneAlbo',
        'regime_fiscale' => 'setRegimeFiscale',
        'sede_nazione' => 'setSedeNazione',
        'sede_numero_civico' => 'setSedeNumeroCivico',
        'stabile_organizzazione_indirizzo' => 'setStabileOrganizzazioneIndirizzo',
        'stabile_organizzazione_numero_civico' => 'setStabileOrganizzazioneNumeroCivico',
        'stabile_organizzazione_cap' => 'setStabileOrganizzazioneCap',
        'stabile_organizzazione_comune' => 'setStabileOrganizzazioneComune',
        'stabile_organizzazione_provincia' => 'setStabileOrganizzazioneProvincia',
        'stabile_organizzazione_nazione' => 'setStabileOrganizzazioneNazione',
        'rea_numero' => 'setReaNumero',
        'rea_ufficio' => 'setReaUfficio',
        'rea_capitale_sociale' => 'setReaCapitaleSociale',
        'rea_socio_unico' => 'setReaSocioUnico',
        'rea_stato_liquidazione' => 'setReaStatoLiquidazione',
        'rappresentante_id_paese' => 'setRappresentanteIdPaese',
        'rappresentante_id_codice' => 'setRappresentanteIdCodice',
        'rappresentante_codice_fiscale' => 'setRappresentanteCodiceFiscale',
        'rappresentante_denominazione' => 'setRappresentanteDenominazione',
        'rappresentante_nome' => 'setRappresentanteNome',
        'rappresentante_cognome' => 'setRappresentanteCognome',
        'rappresentante_titolo' => 'setRappresentanteTitolo',
        'rappresentante_cod_eori' => 'setRappresentanteCodEori',
        'intermediario_id_paese' => 'setIntermediarioIdPaese',
        'intermediario_id_codice' => 'setIntermediarioIdCodice',
        'intermediario_codice_fiscale' => 'setIntermediarioCodiceFiscale',
        'intermediario_denominazione' => 'setIntermediarioDenominazione',
        'intermediario_nome' => 'setIntermediarioNome',
        'intermediario_cognome' => 'setIntermediarioCognome',
        'intermediario_titolo' => 'setIntermediarioTitolo',
        'intermediario_cod_eori' => 'setIntermediarioCodEori'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'soggetto_emittente' => 'getSoggettoEmittente',
        'id_trasmittente_id_paese' => 'getIdTrasmittenteIdPaese',
        'cedente_prestatore_id_paese' => 'getCedentePrestatoreIdPaese',
        'progressivo_invio' => 'getProgressivoInvio',
        'albo_professionale' => 'getAlboProfessionale',
        'provincia_albo' => 'getProvinciaAlbo',
        'numero_iscrizione_albo' => 'getNumeroIscrizioneAlbo',
        'data_iscrizione_albo' => 'getDataIscrizioneAlbo',
        'regime_fiscale' => 'getRegimeFiscale',
        'sede_nazione' => 'getSedeNazione',
        'sede_numero_civico' => 'getSedeNumeroCivico',
        'stabile_organizzazione_indirizzo' => 'getStabileOrganizzazioneIndirizzo',
        'stabile_organizzazione_numero_civico' => 'getStabileOrganizzazioneNumeroCivico',
        'stabile_organizzazione_cap' => 'getStabileOrganizzazioneCap',
        'stabile_organizzazione_comune' => 'getStabileOrganizzazioneComune',
        'stabile_organizzazione_provincia' => 'getStabileOrganizzazioneProvincia',
        'stabile_organizzazione_nazione' => 'getStabileOrganizzazioneNazione',
        'rea_numero' => 'getReaNumero',
        'rea_ufficio' => 'getReaUfficio',
        'rea_capitale_sociale' => 'getReaCapitaleSociale',
        'rea_socio_unico' => 'getReaSocioUnico',
        'rea_stato_liquidazione' => 'getReaStatoLiquidazione',
        'rappresentante_id_paese' => 'getRappresentanteIdPaese',
        'rappresentante_id_codice' => 'getRappresentanteIdCodice',
        'rappresentante_codice_fiscale' => 'getRappresentanteCodiceFiscale',
        'rappresentante_denominazione' => 'getRappresentanteDenominazione',
        'rappresentante_nome' => 'getRappresentanteNome',
        'rappresentante_cognome' => 'getRappresentanteCognome',
        'rappresentante_titolo' => 'getRappresentanteTitolo',
        'rappresentante_cod_eori' => 'getRappresentanteCodEori',
        'intermediario_id_paese' => 'getIntermediarioIdPaese',
        'intermediario_id_codice' => 'getIntermediarioIdCodice',
        'intermediario_codice_fiscale' => 'getIntermediarioCodiceFiscale',
        'intermediario_denominazione' => 'getIntermediarioDenominazione',
        'intermediario_nome' => 'getIntermediarioNome',
        'intermediario_cognome' => 'getIntermediarioCognome',
        'intermediario_titolo' => 'getIntermediarioTitolo',
        'intermediario_cod_eori' => 'getIntermediarioCodEori'
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
        $this->setIfExists('soggetto_emittente', $data ?? [], null);
        $this->setIfExists('id_trasmittente_id_paese', $data ?? [], null);
        $this->setIfExists('cedente_prestatore_id_paese', $data ?? [], null);
        $this->setIfExists('progressivo_invio', $data ?? [], null);
        $this->setIfExists('albo_professionale', $data ?? [], null);
        $this->setIfExists('provincia_albo', $data ?? [], null);
        $this->setIfExists('numero_iscrizione_albo', $data ?? [], null);
        $this->setIfExists('data_iscrizione_albo', $data ?? [], null);
        $this->setIfExists('regime_fiscale', $data ?? [], null);
        $this->setIfExists('sede_nazione', $data ?? [], null);
        $this->setIfExists('sede_numero_civico', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_indirizzo', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_numero_civico', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_cap', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_comune', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_provincia', $data ?? [], null);
        $this->setIfExists('stabile_organizzazione_nazione', $data ?? [], null);
        $this->setIfExists('rea_numero', $data ?? [], null);
        $this->setIfExists('rea_ufficio', $data ?? [], null);
        $this->setIfExists('rea_capitale_sociale', $data ?? [], null);
        $this->setIfExists('rea_socio_unico', $data ?? [], null);
        $this->setIfExists('rea_stato_liquidazione', $data ?? [], null);
        $this->setIfExists('rappresentante_id_paese', $data ?? [], null);
        $this->setIfExists('rappresentante_id_codice', $data ?? [], null);
        $this->setIfExists('rappresentante_codice_fiscale', $data ?? [], null);
        $this->setIfExists('rappresentante_denominazione', $data ?? [], null);
        $this->setIfExists('rappresentante_nome', $data ?? [], null);
        $this->setIfExists('rappresentante_cognome', $data ?? [], null);
        $this->setIfExists('rappresentante_titolo', $data ?? [], null);
        $this->setIfExists('rappresentante_cod_eori', $data ?? [], null);
        $this->setIfExists('intermediario_id_paese', $data ?? [], null);
        $this->setIfExists('intermediario_id_codice', $data ?? [], null);
        $this->setIfExists('intermediario_codice_fiscale', $data ?? [], null);
        $this->setIfExists('intermediario_denominazione', $data ?? [], null);
        $this->setIfExists('intermediario_nome', $data ?? [], null);
        $this->setIfExists('intermediario_cognome', $data ?? [], null);
        $this->setIfExists('intermediario_titolo', $data ?? [], null);
        $this->setIfExists('intermediario_cod_eori', $data ?? [], null);
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

        if (!is_null($this->container['soggetto_emittente']) && (mb_strlen($this->container['soggetto_emittente']) > 2)) {
            $invalidProperties[] = "invalid value for 'soggetto_emittente', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['soggetto_emittente']) && (mb_strlen($this->container['soggetto_emittente']) < 0)) {
            $invalidProperties[] = "invalid value for 'soggetto_emittente', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['id_trasmittente_id_paese']) && (mb_strlen($this->container['id_trasmittente_id_paese']) > 2)) {
            $invalidProperties[] = "invalid value for 'id_trasmittente_id_paese', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['id_trasmittente_id_paese']) && (mb_strlen($this->container['id_trasmittente_id_paese']) < 0)) {
            $invalidProperties[] = "invalid value for 'id_trasmittente_id_paese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['cedente_prestatore_id_paese']) && (mb_strlen($this->container['cedente_prestatore_id_paese']) > 2)) {
            $invalidProperties[] = "invalid value for 'cedente_prestatore_id_paese', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['cedente_prestatore_id_paese']) && (mb_strlen($this->container['cedente_prestatore_id_paese']) < 0)) {
            $invalidProperties[] = "invalid value for 'cedente_prestatore_id_paese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['progressivo_invio']) && (mb_strlen($this->container['progressivo_invio']) > 10)) {
            $invalidProperties[] = "invalid value for 'progressivo_invio', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['progressivo_invio']) && (mb_strlen($this->container['progressivo_invio']) < 0)) {
            $invalidProperties[] = "invalid value for 'progressivo_invio', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['albo_professionale']) && (mb_strlen($this->container['albo_professionale']) > 60)) {
            $invalidProperties[] = "invalid value for 'albo_professionale', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['albo_professionale']) && (mb_strlen($this->container['albo_professionale']) < 0)) {
            $invalidProperties[] = "invalid value for 'albo_professionale', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['provincia_albo']) && (mb_strlen($this->container['provincia_albo']) > 2)) {
            $invalidProperties[] = "invalid value for 'provincia_albo', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['provincia_albo']) && (mb_strlen($this->container['provincia_albo']) < 0)) {
            $invalidProperties[] = "invalid value for 'provincia_albo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['numero_iscrizione_albo']) && (mb_strlen($this->container['numero_iscrizione_albo']) > 60)) {
            $invalidProperties[] = "invalid value for 'numero_iscrizione_albo', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['numero_iscrizione_albo']) && (mb_strlen($this->container['numero_iscrizione_albo']) < 0)) {
            $invalidProperties[] = "invalid value for 'numero_iscrizione_albo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['regime_fiscale']) && (mb_strlen($this->container['regime_fiscale']) > 4)) {
            $invalidProperties[] = "invalid value for 'regime_fiscale', the character length must be smaller than or equal to 4.";
        }

        if (!is_null($this->container['regime_fiscale']) && (mb_strlen($this->container['regime_fiscale']) < 0)) {
            $invalidProperties[] = "invalid value for 'regime_fiscale', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['sede_nazione']) && (mb_strlen($this->container['sede_nazione']) > 2)) {
            $invalidProperties[] = "invalid value for 'sede_nazione', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['sede_nazione']) && (mb_strlen($this->container['sede_nazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'sede_nazione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['sede_numero_civico']) && (mb_strlen($this->container['sede_numero_civico']) > 8)) {
            $invalidProperties[] = "invalid value for 'sede_numero_civico', the character length must be smaller than or equal to 8.";
        }

        if (!is_null($this->container['sede_numero_civico']) && (mb_strlen($this->container['sede_numero_civico']) < 0)) {
            $invalidProperties[] = "invalid value for 'sede_numero_civico', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_indirizzo']) && (mb_strlen($this->container['stabile_organizzazione_indirizzo']) > 60)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_indirizzo', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['stabile_organizzazione_indirizzo']) && (mb_strlen($this->container['stabile_organizzazione_indirizzo']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_indirizzo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_numero_civico']) && (mb_strlen($this->container['stabile_organizzazione_numero_civico']) > 8)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_numero_civico', the character length must be smaller than or equal to 8.";
        }

        if (!is_null($this->container['stabile_organizzazione_numero_civico']) && (mb_strlen($this->container['stabile_organizzazione_numero_civico']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_numero_civico', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_cap']) && (mb_strlen($this->container['stabile_organizzazione_cap']) > 5)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_cap', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['stabile_organizzazione_cap']) && (mb_strlen($this->container['stabile_organizzazione_cap']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_cap', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_comune']) && (mb_strlen($this->container['stabile_organizzazione_comune']) > 60)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_comune', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['stabile_organizzazione_comune']) && (mb_strlen($this->container['stabile_organizzazione_comune']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_comune', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_provincia']) && (mb_strlen($this->container['stabile_organizzazione_provincia']) > 2)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_provincia', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['stabile_organizzazione_provincia']) && (mb_strlen($this->container['stabile_organizzazione_provincia']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_provincia', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['stabile_organizzazione_nazione']) && (mb_strlen($this->container['stabile_organizzazione_nazione']) > 2)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_nazione', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['stabile_organizzazione_nazione']) && (mb_strlen($this->container['stabile_organizzazione_nazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'stabile_organizzazione_nazione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rea_numero']) && (mb_strlen($this->container['rea_numero']) > 20)) {
            $invalidProperties[] = "invalid value for 'rea_numero', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['rea_numero']) && (mb_strlen($this->container['rea_numero']) < 0)) {
            $invalidProperties[] = "invalid value for 'rea_numero', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rea_ufficio']) && (mb_strlen($this->container['rea_ufficio']) > 2)) {
            $invalidProperties[] = "invalid value for 'rea_ufficio', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['rea_ufficio']) && (mb_strlen($this->container['rea_ufficio']) < 0)) {
            $invalidProperties[] = "invalid value for 'rea_ufficio', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rea_socio_unico']) && (mb_strlen($this->container['rea_socio_unico']) > 2)) {
            $invalidProperties[] = "invalid value for 'rea_socio_unico', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['rea_socio_unico']) && (mb_strlen($this->container['rea_socio_unico']) < 0)) {
            $invalidProperties[] = "invalid value for 'rea_socio_unico', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rea_stato_liquidazione']) && (mb_strlen($this->container['rea_stato_liquidazione']) > 2)) {
            $invalidProperties[] = "invalid value for 'rea_stato_liquidazione', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['rea_stato_liquidazione']) && (mb_strlen($this->container['rea_stato_liquidazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'rea_stato_liquidazione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_id_paese']) && (mb_strlen($this->container['rappresentante_id_paese']) > 2)) {
            $invalidProperties[] = "invalid value for 'rappresentante_id_paese', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['rappresentante_id_paese']) && (mb_strlen($this->container['rappresentante_id_paese']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_id_paese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_id_codice']) && (mb_strlen($this->container['rappresentante_id_codice']) > 28)) {
            $invalidProperties[] = "invalid value for 'rappresentante_id_codice', the character length must be smaller than or equal to 28.";
        }

        if (!is_null($this->container['rappresentante_id_codice']) && (mb_strlen($this->container['rappresentante_id_codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_id_codice', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_codice_fiscale']) && (mb_strlen($this->container['rappresentante_codice_fiscale']) > 16)) {
            $invalidProperties[] = "invalid value for 'rappresentante_codice_fiscale', the character length must be smaller than or equal to 16.";
        }

        if (!is_null($this->container['rappresentante_codice_fiscale']) && (mb_strlen($this->container['rappresentante_codice_fiscale']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_codice_fiscale', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_denominazione']) && (mb_strlen($this->container['rappresentante_denominazione']) > 80)) {
            $invalidProperties[] = "invalid value for 'rappresentante_denominazione', the character length must be smaller than or equal to 80.";
        }

        if (!is_null($this->container['rappresentante_denominazione']) && (mb_strlen($this->container['rappresentante_denominazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_denominazione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_nome']) && (mb_strlen($this->container['rappresentante_nome']) > 60)) {
            $invalidProperties[] = "invalid value for 'rappresentante_nome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['rappresentante_nome']) && (mb_strlen($this->container['rappresentante_nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_cognome']) && (mb_strlen($this->container['rappresentante_cognome']) > 60)) {
            $invalidProperties[] = "invalid value for 'rappresentante_cognome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['rappresentante_cognome']) && (mb_strlen($this->container['rappresentante_cognome']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_cognome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_titolo']) && (mb_strlen($this->container['rappresentante_titolo']) > 10)) {
            $invalidProperties[] = "invalid value for 'rappresentante_titolo', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['rappresentante_titolo']) && (mb_strlen($this->container['rappresentante_titolo']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_titolo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['rappresentante_cod_eori']) && (mb_strlen($this->container['rappresentante_cod_eori']) > 17)) {
            $invalidProperties[] = "invalid value for 'rappresentante_cod_eori', the character length must be smaller than or equal to 17.";
        }

        if (!is_null($this->container['rappresentante_cod_eori']) && (mb_strlen($this->container['rappresentante_cod_eori']) < 0)) {
            $invalidProperties[] = "invalid value for 'rappresentante_cod_eori', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_id_paese']) && (mb_strlen($this->container['intermediario_id_paese']) > 2)) {
            $invalidProperties[] = "invalid value for 'intermediario_id_paese', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['intermediario_id_paese']) && (mb_strlen($this->container['intermediario_id_paese']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_id_paese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_id_codice']) && (mb_strlen($this->container['intermediario_id_codice']) > 28)) {
            $invalidProperties[] = "invalid value for 'intermediario_id_codice', the character length must be smaller than or equal to 28.";
        }

        if (!is_null($this->container['intermediario_id_codice']) && (mb_strlen($this->container['intermediario_id_codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_id_codice', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_codice_fiscale']) && (mb_strlen($this->container['intermediario_codice_fiscale']) > 16)) {
            $invalidProperties[] = "invalid value for 'intermediario_codice_fiscale', the character length must be smaller than or equal to 16.";
        }

        if (!is_null($this->container['intermediario_codice_fiscale']) && (mb_strlen($this->container['intermediario_codice_fiscale']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_codice_fiscale', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_denominazione']) && (mb_strlen($this->container['intermediario_denominazione']) > 80)) {
            $invalidProperties[] = "invalid value for 'intermediario_denominazione', the character length must be smaller than or equal to 80.";
        }

        if (!is_null($this->container['intermediario_denominazione']) && (mb_strlen($this->container['intermediario_denominazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_denominazione', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_nome']) && (mb_strlen($this->container['intermediario_nome']) > 60)) {
            $invalidProperties[] = "invalid value for 'intermediario_nome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['intermediario_nome']) && (mb_strlen($this->container['intermediario_nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_cognome']) && (mb_strlen($this->container['intermediario_cognome']) > 60)) {
            $invalidProperties[] = "invalid value for 'intermediario_cognome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['intermediario_cognome']) && (mb_strlen($this->container['intermediario_cognome']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_cognome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_titolo']) && (mb_strlen($this->container['intermediario_titolo']) > 10)) {
            $invalidProperties[] = "invalid value for 'intermediario_titolo', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['intermediario_titolo']) && (mb_strlen($this->container['intermediario_titolo']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_titolo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['intermediario_cod_eori']) && (mb_strlen($this->container['intermediario_cod_eori']) > 17)) {
            $invalidProperties[] = "invalid value for 'intermediario_cod_eori', the character length must be smaller than or equal to 17.";
        }

        if (!is_null($this->container['intermediario_cod_eori']) && (mb_strlen($this->container['intermediario_cod_eori']) < 0)) {
            $invalidProperties[] = "invalid value for 'intermediario_cod_eori', the character length must be bigger than or equal to 0.";
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
     * Gets soggetto_emittente
     *
     * @return string|null
     */
    public function getSoggettoEmittente()
    {
        return $this->container['soggetto_emittente'];
    }

    /**
     * Sets soggetto_emittente
     *
     * @param string|null $soggetto_emittente soggetto_emittente
     *
     * @return self
     */
    public function setSoggettoEmittente($soggetto_emittente)
    {
        if (is_null($soggetto_emittente)) {
            array_push($this->openAPINullablesSetToNull, 'soggetto_emittente');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('soggetto_emittente', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($soggetto_emittente) && (mb_strlen($soggetto_emittente) > 2)) {
            throw new \InvalidArgumentException('invalid length for $soggetto_emittente when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($soggetto_emittente) && (mb_strlen($soggetto_emittente) < 0)) {
            throw new \InvalidArgumentException('invalid length for $soggetto_emittente when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['soggetto_emittente'] = $soggetto_emittente;

        return $this;
    }

    /**
     * Gets id_trasmittente_id_paese
     *
     * @return string|null
     */
    public function getIdTrasmittenteIdPaese()
    {
        return $this->container['id_trasmittente_id_paese'];
    }

    /**
     * Sets id_trasmittente_id_paese
     *
     * @param string|null $id_trasmittente_id_paese id_trasmittente_id_paese
     *
     * @return self
     */
    public function setIdTrasmittenteIdPaese($id_trasmittente_id_paese)
    {
        if (is_null($id_trasmittente_id_paese)) {
            array_push($this->openAPINullablesSetToNull, 'id_trasmittente_id_paese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('id_trasmittente_id_paese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($id_trasmittente_id_paese) && (mb_strlen($id_trasmittente_id_paese) > 2)) {
            throw new \InvalidArgumentException('invalid length for $id_trasmittente_id_paese when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($id_trasmittente_id_paese) && (mb_strlen($id_trasmittente_id_paese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $id_trasmittente_id_paese when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['id_trasmittente_id_paese'] = $id_trasmittente_id_paese;

        return $this;
    }

    /**
     * Gets cedente_prestatore_id_paese
     *
     * @return string|null
     */
    public function getCedentePrestatoreIdPaese()
    {
        return $this->container['cedente_prestatore_id_paese'];
    }

    /**
     * Sets cedente_prestatore_id_paese
     *
     * @param string|null $cedente_prestatore_id_paese cedente_prestatore_id_paese
     *
     * @return self
     */
    public function setCedentePrestatoreIdPaese($cedente_prestatore_id_paese)
    {
        if (is_null($cedente_prestatore_id_paese)) {
            array_push($this->openAPINullablesSetToNull, 'cedente_prestatore_id_paese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cedente_prestatore_id_paese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($cedente_prestatore_id_paese) && (mb_strlen($cedente_prestatore_id_paese) > 2)) {
            throw new \InvalidArgumentException('invalid length for $cedente_prestatore_id_paese when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($cedente_prestatore_id_paese) && (mb_strlen($cedente_prestatore_id_paese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $cedente_prestatore_id_paese when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['cedente_prestatore_id_paese'] = $cedente_prestatore_id_paese;

        return $this;
    }

    /**
     * Gets progressivo_invio
     *
     * @return string|null
     */
    public function getProgressivoInvio()
    {
        return $this->container['progressivo_invio'];
    }

    /**
     * Sets progressivo_invio
     *
     * @param string|null $progressivo_invio progressivo_invio
     *
     * @return self
     */
    public function setProgressivoInvio($progressivo_invio)
    {
        if (is_null($progressivo_invio)) {
            array_push($this->openAPINullablesSetToNull, 'progressivo_invio');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('progressivo_invio', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($progressivo_invio) && (mb_strlen($progressivo_invio) > 10)) {
            throw new \InvalidArgumentException('invalid length for $progressivo_invio when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 10.');
        }
        if (!is_null($progressivo_invio) && (mb_strlen($progressivo_invio) < 0)) {
            throw new \InvalidArgumentException('invalid length for $progressivo_invio when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['progressivo_invio'] = $progressivo_invio;

        return $this;
    }

    /**
     * Gets albo_professionale
     *
     * @return string|null
     */
    public function getAlboProfessionale()
    {
        return $this->container['albo_professionale'];
    }

    /**
     * Sets albo_professionale
     *
     * @param string|null $albo_professionale albo_professionale
     *
     * @return self
     */
    public function setAlboProfessionale($albo_professionale)
    {
        if (is_null($albo_professionale)) {
            array_push($this->openAPINullablesSetToNull, 'albo_professionale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('albo_professionale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($albo_professionale) && (mb_strlen($albo_professionale) > 60)) {
            throw new \InvalidArgumentException('invalid length for $albo_professionale when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($albo_professionale) && (mb_strlen($albo_professionale) < 0)) {
            throw new \InvalidArgumentException('invalid length for $albo_professionale when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['albo_professionale'] = $albo_professionale;

        return $this;
    }

    /**
     * Gets provincia_albo
     *
     * @return string|null
     */
    public function getProvinciaAlbo()
    {
        return $this->container['provincia_albo'];
    }

    /**
     * Sets provincia_albo
     *
     * @param string|null $provincia_albo provincia_albo
     *
     * @return self
     */
    public function setProvinciaAlbo($provincia_albo)
    {
        if (is_null($provincia_albo)) {
            array_push($this->openAPINullablesSetToNull, 'provincia_albo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provincia_albo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($provincia_albo) && (mb_strlen($provincia_albo) > 2)) {
            throw new \InvalidArgumentException('invalid length for $provincia_albo when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($provincia_albo) && (mb_strlen($provincia_albo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $provincia_albo when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['provincia_albo'] = $provincia_albo;

        return $this;
    }

    /**
     * Gets numero_iscrizione_albo
     *
     * @return string|null
     */
    public function getNumeroIscrizioneAlbo()
    {
        return $this->container['numero_iscrizione_albo'];
    }

    /**
     * Sets numero_iscrizione_albo
     *
     * @param string|null $numero_iscrizione_albo numero_iscrizione_albo
     *
     * @return self
     */
    public function setNumeroIscrizioneAlbo($numero_iscrizione_albo)
    {
        if (is_null($numero_iscrizione_albo)) {
            array_push($this->openAPINullablesSetToNull, 'numero_iscrizione_albo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('numero_iscrizione_albo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($numero_iscrizione_albo) && (mb_strlen($numero_iscrizione_albo) > 60)) {
            throw new \InvalidArgumentException('invalid length for $numero_iscrizione_albo when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($numero_iscrizione_albo) && (mb_strlen($numero_iscrizione_albo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $numero_iscrizione_albo when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['numero_iscrizione_albo'] = $numero_iscrizione_albo;

        return $this;
    }

    /**
     * Gets data_iscrizione_albo
     *
     * @return \DateTime|null
     */
    public function getDataIscrizioneAlbo()
    {
        return $this->container['data_iscrizione_albo'];
    }

    /**
     * Sets data_iscrizione_albo
     *
     * @param \DateTime|null $data_iscrizione_albo data_iscrizione_albo
     *
     * @return self
     */
    public function setDataIscrizioneAlbo($data_iscrizione_albo)
    {
        if (is_null($data_iscrizione_albo)) {
            array_push($this->openAPINullablesSetToNull, 'data_iscrizione_albo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_iscrizione_albo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_iscrizione_albo'] = $data_iscrizione_albo;

        return $this;
    }

    /**
     * Gets regime_fiscale
     *
     * @return string|null
     */
    public function getRegimeFiscale()
    {
        return $this->container['regime_fiscale'];
    }

    /**
     * Sets regime_fiscale
     *
     * @param string|null $regime_fiscale regime_fiscale
     *
     * @return self
     */
    public function setRegimeFiscale($regime_fiscale)
    {
        if (is_null($regime_fiscale)) {
            array_push($this->openAPINullablesSetToNull, 'regime_fiscale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('regime_fiscale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($regime_fiscale) && (mb_strlen($regime_fiscale) > 4)) {
            throw new \InvalidArgumentException('invalid length for $regime_fiscale when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 4.');
        }
        if (!is_null($regime_fiscale) && (mb_strlen($regime_fiscale) < 0)) {
            throw new \InvalidArgumentException('invalid length for $regime_fiscale when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['regime_fiscale'] = $regime_fiscale;

        return $this;
    }

    /**
     * Gets sede_nazione
     *
     * @return string|null
     */
    public function getSedeNazione()
    {
        return $this->container['sede_nazione'];
    }

    /**
     * Sets sede_nazione
     *
     * @param string|null $sede_nazione sede_nazione
     *
     * @return self
     */
    public function setSedeNazione($sede_nazione)
    {
        if (is_null($sede_nazione)) {
            array_push($this->openAPINullablesSetToNull, 'sede_nazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sede_nazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($sede_nazione) && (mb_strlen($sede_nazione) > 2)) {
            throw new \InvalidArgumentException('invalid length for $sede_nazione when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($sede_nazione) && (mb_strlen($sede_nazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $sede_nazione when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['sede_nazione'] = $sede_nazione;

        return $this;
    }

    /**
     * Gets sede_numero_civico
     *
     * @return string|null
     */
    public function getSedeNumeroCivico()
    {
        return $this->container['sede_numero_civico'];
    }

    /**
     * Sets sede_numero_civico
     *
     * @param string|null $sede_numero_civico sede_numero_civico
     *
     * @return self
     */
    public function setSedeNumeroCivico($sede_numero_civico)
    {
        if (is_null($sede_numero_civico)) {
            array_push($this->openAPINullablesSetToNull, 'sede_numero_civico');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sede_numero_civico', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($sede_numero_civico) && (mb_strlen($sede_numero_civico) > 8)) {
            throw new \InvalidArgumentException('invalid length for $sede_numero_civico when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 8.');
        }
        if (!is_null($sede_numero_civico) && (mb_strlen($sede_numero_civico) < 0)) {
            throw new \InvalidArgumentException('invalid length for $sede_numero_civico when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['sede_numero_civico'] = $sede_numero_civico;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_indirizzo
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneIndirizzo()
    {
        return $this->container['stabile_organizzazione_indirizzo'];
    }

    /**
     * Sets stabile_organizzazione_indirizzo
     *
     * @param string|null $stabile_organizzazione_indirizzo stabile_organizzazione_indirizzo
     *
     * @return self
     */
    public function setStabileOrganizzazioneIndirizzo($stabile_organizzazione_indirizzo)
    {
        if (is_null($stabile_organizzazione_indirizzo)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_indirizzo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_indirizzo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_indirizzo) && (mb_strlen($stabile_organizzazione_indirizzo) > 60)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_indirizzo when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($stabile_organizzazione_indirizzo) && (mb_strlen($stabile_organizzazione_indirizzo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_indirizzo when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_indirizzo'] = $stabile_organizzazione_indirizzo;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_numero_civico
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneNumeroCivico()
    {
        return $this->container['stabile_organizzazione_numero_civico'];
    }

    /**
     * Sets stabile_organizzazione_numero_civico
     *
     * @param string|null $stabile_organizzazione_numero_civico stabile_organizzazione_numero_civico
     *
     * @return self
     */
    public function setStabileOrganizzazioneNumeroCivico($stabile_organizzazione_numero_civico)
    {
        if (is_null($stabile_organizzazione_numero_civico)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_numero_civico');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_numero_civico', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_numero_civico) && (mb_strlen($stabile_organizzazione_numero_civico) > 8)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_numero_civico when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 8.');
        }
        if (!is_null($stabile_organizzazione_numero_civico) && (mb_strlen($stabile_organizzazione_numero_civico) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_numero_civico when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_numero_civico'] = $stabile_organizzazione_numero_civico;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_cap
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneCap()
    {
        return $this->container['stabile_organizzazione_cap'];
    }

    /**
     * Sets stabile_organizzazione_cap
     *
     * @param string|null $stabile_organizzazione_cap stabile_organizzazione_cap
     *
     * @return self
     */
    public function setStabileOrganizzazioneCap($stabile_organizzazione_cap)
    {
        if (is_null($stabile_organizzazione_cap)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_cap');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_cap', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_cap) && (mb_strlen($stabile_organizzazione_cap) > 5)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_cap when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 5.');
        }
        if (!is_null($stabile_organizzazione_cap) && (mb_strlen($stabile_organizzazione_cap) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_cap when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_cap'] = $stabile_organizzazione_cap;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_comune
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneComune()
    {
        return $this->container['stabile_organizzazione_comune'];
    }

    /**
     * Sets stabile_organizzazione_comune
     *
     * @param string|null $stabile_organizzazione_comune stabile_organizzazione_comune
     *
     * @return self
     */
    public function setStabileOrganizzazioneComune($stabile_organizzazione_comune)
    {
        if (is_null($stabile_organizzazione_comune)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_comune');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_comune', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_comune) && (mb_strlen($stabile_organizzazione_comune) > 60)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_comune when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($stabile_organizzazione_comune) && (mb_strlen($stabile_organizzazione_comune) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_comune when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_comune'] = $stabile_organizzazione_comune;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_provincia
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneProvincia()
    {
        return $this->container['stabile_organizzazione_provincia'];
    }

    /**
     * Sets stabile_organizzazione_provincia
     *
     * @param string|null $stabile_organizzazione_provincia stabile_organizzazione_provincia
     *
     * @return self
     */
    public function setStabileOrganizzazioneProvincia($stabile_organizzazione_provincia)
    {
        if (is_null($stabile_organizzazione_provincia)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_provincia');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_provincia', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_provincia) && (mb_strlen($stabile_organizzazione_provincia) > 2)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_provincia when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($stabile_organizzazione_provincia) && (mb_strlen($stabile_organizzazione_provincia) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_provincia when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_provincia'] = $stabile_organizzazione_provincia;

        return $this;
    }

    /**
     * Gets stabile_organizzazione_nazione
     *
     * @return string|null
     */
    public function getStabileOrganizzazioneNazione()
    {
        return $this->container['stabile_organizzazione_nazione'];
    }

    /**
     * Sets stabile_organizzazione_nazione
     *
     * @param string|null $stabile_organizzazione_nazione stabile_organizzazione_nazione
     *
     * @return self
     */
    public function setStabileOrganizzazioneNazione($stabile_organizzazione_nazione)
    {
        if (is_null($stabile_organizzazione_nazione)) {
            array_push($this->openAPINullablesSetToNull, 'stabile_organizzazione_nazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('stabile_organizzazione_nazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($stabile_organizzazione_nazione) && (mb_strlen($stabile_organizzazione_nazione) > 2)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_nazione when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($stabile_organizzazione_nazione) && (mb_strlen($stabile_organizzazione_nazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $stabile_organizzazione_nazione when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['stabile_organizzazione_nazione'] = $stabile_organizzazione_nazione;

        return $this;
    }

    /**
     * Gets rea_numero
     *
     * @return string|null
     */
    public function getReaNumero()
    {
        return $this->container['rea_numero'];
    }

    /**
     * Sets rea_numero
     *
     * @param string|null $rea_numero rea_numero
     *
     * @return self
     */
    public function setReaNumero($rea_numero)
    {
        if (is_null($rea_numero)) {
            array_push($this->openAPINullablesSetToNull, 'rea_numero');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rea_numero', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rea_numero) && (mb_strlen($rea_numero) > 20)) {
            throw new \InvalidArgumentException('invalid length for $rea_numero when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 20.');
        }
        if (!is_null($rea_numero) && (mb_strlen($rea_numero) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rea_numero when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rea_numero'] = $rea_numero;

        return $this;
    }

    /**
     * Gets rea_ufficio
     *
     * @return string|null
     */
    public function getReaUfficio()
    {
        return $this->container['rea_ufficio'];
    }

    /**
     * Sets rea_ufficio
     *
     * @param string|null $rea_ufficio rea_ufficio
     *
     * @return self
     */
    public function setReaUfficio($rea_ufficio)
    {
        if (is_null($rea_ufficio)) {
            array_push($this->openAPINullablesSetToNull, 'rea_ufficio');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rea_ufficio', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rea_ufficio) && (mb_strlen($rea_ufficio) > 2)) {
            throw new \InvalidArgumentException('invalid length for $rea_ufficio when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($rea_ufficio) && (mb_strlen($rea_ufficio) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rea_ufficio when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rea_ufficio'] = $rea_ufficio;

        return $this;
    }

    /**
     * Gets rea_capitale_sociale
     *
     * @return float|null
     */
    public function getReaCapitaleSociale()
    {
        return $this->container['rea_capitale_sociale'];
    }

    /**
     * Sets rea_capitale_sociale
     *
     * @param float|null $rea_capitale_sociale rea_capitale_sociale
     *
     * @return self
     */
    public function setReaCapitaleSociale($rea_capitale_sociale)
    {
        if (is_null($rea_capitale_sociale)) {
            array_push($this->openAPINullablesSetToNull, 'rea_capitale_sociale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rea_capitale_sociale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['rea_capitale_sociale'] = $rea_capitale_sociale;

        return $this;
    }

    /**
     * Gets rea_socio_unico
     *
     * @return string|null
     */
    public function getReaSocioUnico()
    {
        return $this->container['rea_socio_unico'];
    }

    /**
     * Sets rea_socio_unico
     *
     * @param string|null $rea_socio_unico rea_socio_unico
     *
     * @return self
     */
    public function setReaSocioUnico($rea_socio_unico)
    {
        if (is_null($rea_socio_unico)) {
            array_push($this->openAPINullablesSetToNull, 'rea_socio_unico');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rea_socio_unico', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rea_socio_unico) && (mb_strlen($rea_socio_unico) > 2)) {
            throw new \InvalidArgumentException('invalid length for $rea_socio_unico when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($rea_socio_unico) && (mb_strlen($rea_socio_unico) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rea_socio_unico when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rea_socio_unico'] = $rea_socio_unico;

        return $this;
    }

    /**
     * Gets rea_stato_liquidazione
     *
     * @return string|null
     */
    public function getReaStatoLiquidazione()
    {
        return $this->container['rea_stato_liquidazione'];
    }

    /**
     * Sets rea_stato_liquidazione
     *
     * @param string|null $rea_stato_liquidazione rea_stato_liquidazione
     *
     * @return self
     */
    public function setReaStatoLiquidazione($rea_stato_liquidazione)
    {
        if (is_null($rea_stato_liquidazione)) {
            array_push($this->openAPINullablesSetToNull, 'rea_stato_liquidazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rea_stato_liquidazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rea_stato_liquidazione) && (mb_strlen($rea_stato_liquidazione) > 2)) {
            throw new \InvalidArgumentException('invalid length for $rea_stato_liquidazione when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($rea_stato_liquidazione) && (mb_strlen($rea_stato_liquidazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rea_stato_liquidazione when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rea_stato_liquidazione'] = $rea_stato_liquidazione;

        return $this;
    }

    /**
     * Gets rappresentante_id_paese
     *
     * @return string|null
     */
    public function getRappresentanteIdPaese()
    {
        return $this->container['rappresentante_id_paese'];
    }

    /**
     * Sets rappresentante_id_paese
     *
     * @param string|null $rappresentante_id_paese rappresentante_id_paese
     *
     * @return self
     */
    public function setRappresentanteIdPaese($rappresentante_id_paese)
    {
        if (is_null($rappresentante_id_paese)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_id_paese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_id_paese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_id_paese) && (mb_strlen($rappresentante_id_paese) > 2)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_id_paese when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($rappresentante_id_paese) && (mb_strlen($rappresentante_id_paese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_id_paese when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_id_paese'] = $rappresentante_id_paese;

        return $this;
    }

    /**
     * Gets rappresentante_id_codice
     *
     * @return string|null
     */
    public function getRappresentanteIdCodice()
    {
        return $this->container['rappresentante_id_codice'];
    }

    /**
     * Sets rappresentante_id_codice
     *
     * @param string|null $rappresentante_id_codice rappresentante_id_codice
     *
     * @return self
     */
    public function setRappresentanteIdCodice($rappresentante_id_codice)
    {
        if (is_null($rappresentante_id_codice)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_id_codice');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_id_codice', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_id_codice) && (mb_strlen($rappresentante_id_codice) > 28)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_id_codice when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 28.');
        }
        if (!is_null($rappresentante_id_codice) && (mb_strlen($rappresentante_id_codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_id_codice when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_id_codice'] = $rappresentante_id_codice;

        return $this;
    }

    /**
     * Gets rappresentante_codice_fiscale
     *
     * @return string|null
     */
    public function getRappresentanteCodiceFiscale()
    {
        return $this->container['rappresentante_codice_fiscale'];
    }

    /**
     * Sets rappresentante_codice_fiscale
     *
     * @param string|null $rappresentante_codice_fiscale rappresentante_codice_fiscale
     *
     * @return self
     */
    public function setRappresentanteCodiceFiscale($rappresentante_codice_fiscale)
    {
        if (is_null($rappresentante_codice_fiscale)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_codice_fiscale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_codice_fiscale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_codice_fiscale) && (mb_strlen($rappresentante_codice_fiscale) > 16)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_codice_fiscale when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 16.');
        }
        if (!is_null($rappresentante_codice_fiscale) && (mb_strlen($rappresentante_codice_fiscale) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_codice_fiscale when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_codice_fiscale'] = $rappresentante_codice_fiscale;

        return $this;
    }

    /**
     * Gets rappresentante_denominazione
     *
     * @return string|null
     */
    public function getRappresentanteDenominazione()
    {
        return $this->container['rappresentante_denominazione'];
    }

    /**
     * Sets rappresentante_denominazione
     *
     * @param string|null $rappresentante_denominazione rappresentante_denominazione
     *
     * @return self
     */
    public function setRappresentanteDenominazione($rappresentante_denominazione)
    {
        if (is_null($rappresentante_denominazione)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_denominazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_denominazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_denominazione) && (mb_strlen($rappresentante_denominazione) > 80)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_denominazione when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 80.');
        }
        if (!is_null($rappresentante_denominazione) && (mb_strlen($rappresentante_denominazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_denominazione when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_denominazione'] = $rappresentante_denominazione;

        return $this;
    }

    /**
     * Gets rappresentante_nome
     *
     * @return string|null
     */
    public function getRappresentanteNome()
    {
        return $this->container['rappresentante_nome'];
    }

    /**
     * Sets rappresentante_nome
     *
     * @param string|null $rappresentante_nome rappresentante_nome
     *
     * @return self
     */
    public function setRappresentanteNome($rappresentante_nome)
    {
        if (is_null($rappresentante_nome)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_nome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_nome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_nome) && (mb_strlen($rappresentante_nome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_nome when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($rappresentante_nome) && (mb_strlen($rappresentante_nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_nome when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_nome'] = $rappresentante_nome;

        return $this;
    }

    /**
     * Gets rappresentante_cognome
     *
     * @return string|null
     */
    public function getRappresentanteCognome()
    {
        return $this->container['rappresentante_cognome'];
    }

    /**
     * Sets rappresentante_cognome
     *
     * @param string|null $rappresentante_cognome rappresentante_cognome
     *
     * @return self
     */
    public function setRappresentanteCognome($rappresentante_cognome)
    {
        if (is_null($rappresentante_cognome)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_cognome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_cognome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_cognome) && (mb_strlen($rappresentante_cognome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_cognome when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($rappresentante_cognome) && (mb_strlen($rappresentante_cognome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_cognome when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_cognome'] = $rappresentante_cognome;

        return $this;
    }

    /**
     * Gets rappresentante_titolo
     *
     * @return string|null
     */
    public function getRappresentanteTitolo()
    {
        return $this->container['rappresentante_titolo'];
    }

    /**
     * Sets rappresentante_titolo
     *
     * @param string|null $rappresentante_titolo rappresentante_titolo
     *
     * @return self
     */
    public function setRappresentanteTitolo($rappresentante_titolo)
    {
        if (is_null($rappresentante_titolo)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_titolo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_titolo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_titolo) && (mb_strlen($rappresentante_titolo) > 10)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_titolo when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 10.');
        }
        if (!is_null($rappresentante_titolo) && (mb_strlen($rappresentante_titolo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_titolo when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_titolo'] = $rappresentante_titolo;

        return $this;
    }

    /**
     * Gets rappresentante_cod_eori
     *
     * @return string|null
     */
    public function getRappresentanteCodEori()
    {
        return $this->container['rappresentante_cod_eori'];
    }

    /**
     * Sets rappresentante_cod_eori
     *
     * @param string|null $rappresentante_cod_eori rappresentante_cod_eori
     *
     * @return self
     */
    public function setRappresentanteCodEori($rappresentante_cod_eori)
    {
        if (is_null($rappresentante_cod_eori)) {
            array_push($this->openAPINullablesSetToNull, 'rappresentante_cod_eori');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('rappresentante_cod_eori', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($rappresentante_cod_eori) && (mb_strlen($rappresentante_cod_eori) > 17)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_cod_eori when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 17.');
        }
        if (!is_null($rappresentante_cod_eori) && (mb_strlen($rappresentante_cod_eori) < 0)) {
            throw new \InvalidArgumentException('invalid length for $rappresentante_cod_eori when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['rappresentante_cod_eori'] = $rappresentante_cod_eori;

        return $this;
    }

    /**
     * Gets intermediario_id_paese
     *
     * @return string|null
     */
    public function getIntermediarioIdPaese()
    {
        return $this->container['intermediario_id_paese'];
    }

    /**
     * Sets intermediario_id_paese
     *
     * @param string|null $intermediario_id_paese intermediario_id_paese
     *
     * @return self
     */
    public function setIntermediarioIdPaese($intermediario_id_paese)
    {
        if (is_null($intermediario_id_paese)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_id_paese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_id_paese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_id_paese) && (mb_strlen($intermediario_id_paese) > 2)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_id_paese when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 2.');
        }
        if (!is_null($intermediario_id_paese) && (mb_strlen($intermediario_id_paese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_id_paese when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_id_paese'] = $intermediario_id_paese;

        return $this;
    }

    /**
     * Gets intermediario_id_codice
     *
     * @return string|null
     */
    public function getIntermediarioIdCodice()
    {
        return $this->container['intermediario_id_codice'];
    }

    /**
     * Sets intermediario_id_codice
     *
     * @param string|null $intermediario_id_codice intermediario_id_codice
     *
     * @return self
     */
    public function setIntermediarioIdCodice($intermediario_id_codice)
    {
        if (is_null($intermediario_id_codice)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_id_codice');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_id_codice', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_id_codice) && (mb_strlen($intermediario_id_codice) > 28)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_id_codice when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 28.');
        }
        if (!is_null($intermediario_id_codice) && (mb_strlen($intermediario_id_codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_id_codice when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_id_codice'] = $intermediario_id_codice;

        return $this;
    }

    /**
     * Gets intermediario_codice_fiscale
     *
     * @return string|null
     */
    public function getIntermediarioCodiceFiscale()
    {
        return $this->container['intermediario_codice_fiscale'];
    }

    /**
     * Sets intermediario_codice_fiscale
     *
     * @param string|null $intermediario_codice_fiscale intermediario_codice_fiscale
     *
     * @return self
     */
    public function setIntermediarioCodiceFiscale($intermediario_codice_fiscale)
    {
        if (is_null($intermediario_codice_fiscale)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_codice_fiscale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_codice_fiscale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_codice_fiscale) && (mb_strlen($intermediario_codice_fiscale) > 16)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_codice_fiscale when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 16.');
        }
        if (!is_null($intermediario_codice_fiscale) && (mb_strlen($intermediario_codice_fiscale) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_codice_fiscale when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_codice_fiscale'] = $intermediario_codice_fiscale;

        return $this;
    }

    /**
     * Gets intermediario_denominazione
     *
     * @return string|null
     */
    public function getIntermediarioDenominazione()
    {
        return $this->container['intermediario_denominazione'];
    }

    /**
     * Sets intermediario_denominazione
     *
     * @param string|null $intermediario_denominazione intermediario_denominazione
     *
     * @return self
     */
    public function setIntermediarioDenominazione($intermediario_denominazione)
    {
        if (is_null($intermediario_denominazione)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_denominazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_denominazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_denominazione) && (mb_strlen($intermediario_denominazione) > 80)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_denominazione when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 80.');
        }
        if (!is_null($intermediario_denominazione) && (mb_strlen($intermediario_denominazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_denominazione when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_denominazione'] = $intermediario_denominazione;

        return $this;
    }

    /**
     * Gets intermediario_nome
     *
     * @return string|null
     */
    public function getIntermediarioNome()
    {
        return $this->container['intermediario_nome'];
    }

    /**
     * Sets intermediario_nome
     *
     * @param string|null $intermediario_nome intermediario_nome
     *
     * @return self
     */
    public function setIntermediarioNome($intermediario_nome)
    {
        if (is_null($intermediario_nome)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_nome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_nome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_nome) && (mb_strlen($intermediario_nome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_nome when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($intermediario_nome) && (mb_strlen($intermediario_nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_nome when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_nome'] = $intermediario_nome;

        return $this;
    }

    /**
     * Gets intermediario_cognome
     *
     * @return string|null
     */
    public function getIntermediarioCognome()
    {
        return $this->container['intermediario_cognome'];
    }

    /**
     * Sets intermediario_cognome
     *
     * @param string|null $intermediario_cognome intermediario_cognome
     *
     * @return self
     */
    public function setIntermediarioCognome($intermediario_cognome)
    {
        if (is_null($intermediario_cognome)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_cognome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_cognome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_cognome) && (mb_strlen($intermediario_cognome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_cognome when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 60.');
        }
        if (!is_null($intermediario_cognome) && (mb_strlen($intermediario_cognome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_cognome when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_cognome'] = $intermediario_cognome;

        return $this;
    }

    /**
     * Gets intermediario_titolo
     *
     * @return string|null
     */
    public function getIntermediarioTitolo()
    {
        return $this->container['intermediario_titolo'];
    }

    /**
     * Sets intermediario_titolo
     *
     * @param string|null $intermediario_titolo intermediario_titolo
     *
     * @return self
     */
    public function setIntermediarioTitolo($intermediario_titolo)
    {
        if (is_null($intermediario_titolo)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_titolo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_titolo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_titolo) && (mb_strlen($intermediario_titolo) > 10)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_titolo when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 10.');
        }
        if (!is_null($intermediario_titolo) && (mb_strlen($intermediario_titolo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_titolo when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_titolo'] = $intermediario_titolo;

        return $this;
    }

    /**
     * Gets intermediario_cod_eori
     *
     * @return string|null
     */
    public function getIntermediarioCodEori()
    {
        return $this->container['intermediario_cod_eori'];
    }

    /**
     * Sets intermediario_cod_eori
     *
     * @param string|null $intermediario_cod_eori intermediario_cod_eori
     *
     * @return self
     */
    public function setIntermediarioCodEori($intermediario_cod_eori)
    {
        if (is_null($intermediario_cod_eori)) {
            array_push($this->openAPINullablesSetToNull, 'intermediario_cod_eori');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('intermediario_cod_eori', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($intermediario_cod_eori) && (mb_strlen($intermediario_cod_eori) > 17)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_cod_eori when calling FatturaElettronicaXmlTemplate., must be smaller than or equal to 17.');
        }
        if (!is_null($intermediario_cod_eori) && (mb_strlen($intermediario_cod_eori) < 0)) {
            throw new \InvalidArgumentException('invalid length for $intermediario_cod_eori when calling FatturaElettronicaXmlTemplate., must be bigger than or equal to 0.');
        }

        $this->container['intermediario_cod_eori'] = $intermediario_cod_eori;

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


