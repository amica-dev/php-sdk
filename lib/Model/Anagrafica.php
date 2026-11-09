<?php
/**
 * Anagrafica
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
 * The version of the OpenAPI document: 3.3.4
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
 * Anagrafica Class Doc Comment
 *
 * @category Class
 * @description Anagrafica di cliente, fornitore, agente o vettore.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Anagrafica implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Anagrafica';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'modello_id' => 'int',
        'nazione_id' => 'int',
        'nazione' => '\Amica\Sdk\Model\Nazione',
        'area_geografica_id' => 'int',
        'area_geografica' => '\Amica\Sdk\Model\AreaGeografica',
        'valuta_id' => 'int',
        'valuta' => '\Amica\Sdk\Model\Valuta',
        'indirizzo_predefinito_id' => 'int',
        'indirizzi' => '\Amica\Sdk\Model\Indirizzo[]',
        'scheda_cliente' => '\Amica\Sdk\Model\SchedaCliente',
        'scheda_fornitore' => '\Amica\Sdk\Model\SchedaFornitore',
        'stato' => 'bool',
        'cliente' => 'bool',
        'fornitore' => 'bool',
        'agente' => 'bool',
        'vettore' => 'bool',
        'capo_area' => 'bool',
        'persona_giuridica' => 'bool',
        'banca' => '\Amica\Sdk\Model\Banca',
        'nominativo1' => '\Amica\Sdk\Model\AnagraficaNominativo1',
        'nominativo2' => '\Amica\Sdk\Model\AnagraficaNominativo2',
        'sede_legale' => '\Amica\Sdk\Model\AnagraficaSedeLegale',
        'codice' => 'string',
        'data_inserimento' => '\DateTime',
        'ragione_sociale1' => 'string',
        'ragione_sociale2' => 'string',
        'e_mail' => 'string',
        'sito_web' => 'string',
        'partita_iva' => 'string',
        'codice_fiscale' => 'string',
        'note' => 'string',
        'note_importanti' => 'string',
        'pec' => 'string',
        'codice_destinatario' => 'string',
        'dichiarazione_intento' => 'string',
        'riferimento_amministrazione' => 'string'
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
        'modello_id' => 'int32',
        'nazione_id' => 'int32',
        'nazione' => null,
        'area_geografica_id' => 'int32',
        'area_geografica' => null,
        'valuta_id' => 'int32',
        'valuta' => null,
        'indirizzo_predefinito_id' => 'int32',
        'indirizzi' => null,
        'scheda_cliente' => null,
        'scheda_fornitore' => null,
        'stato' => null,
        'cliente' => null,
        'fornitore' => null,
        'agente' => null,
        'vettore' => null,
        'capo_area' => null,
        'persona_giuridica' => null,
        'banca' => null,
        'nominativo1' => null,
        'nominativo2' => null,
        'sede_legale' => null,
        'codice' => null,
        'data_inserimento' => 'date-time',
        'ragione_sociale1' => null,
        'ragione_sociale2' => null,
        'e_mail' => null,
        'sito_web' => null,
        'partita_iva' => null,
        'codice_fiscale' => null,
        'note' => null,
        'note_importanti' => null,
        'pec' => null,
        'codice_destinatario' => null,
        'dichiarazione_intento' => null,
        'riferimento_amministrazione' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'modello_id' => true,
        'nazione_id' => true,
        'nazione' => false,
        'area_geografica_id' => true,
        'area_geografica' => false,
        'valuta_id' => true,
        'valuta' => false,
        'indirizzo_predefinito_id' => true,
        'indirizzi' => true,
        'scheda_cliente' => false,
        'scheda_fornitore' => false,
        'stato' => false,
        'cliente' => false,
        'fornitore' => false,
        'agente' => false,
        'vettore' => false,
        'capo_area' => false,
        'persona_giuridica' => false,
        'banca' => false,
        'nominativo1' => false,
        'nominativo2' => false,
        'sede_legale' => false,
        'codice' => true,
        'data_inserimento' => true,
        'ragione_sociale1' => false,
        'ragione_sociale2' => true,
        'e_mail' => true,
        'sito_web' => true,
        'partita_iva' => true,
        'codice_fiscale' => true,
        'note' => true,
        'note_importanti' => true,
        'pec' => true,
        'codice_destinatario' => true,
        'dichiarazione_intento' => true,
        'riferimento_amministrazione' => true
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
        'modello_id' => 'modelloId',
        'nazione_id' => 'nazioneId',
        'nazione' => 'nazione',
        'area_geografica_id' => 'areaGeograficaId',
        'area_geografica' => 'areaGeografica',
        'valuta_id' => 'valutaId',
        'valuta' => 'valuta',
        'indirizzo_predefinito_id' => 'indirizzoPredefinitoId',
        'indirizzi' => 'indirizzi',
        'scheda_cliente' => 'schedaCliente',
        'scheda_fornitore' => 'schedaFornitore',
        'stato' => 'stato',
        'cliente' => 'cliente',
        'fornitore' => 'fornitore',
        'agente' => 'agente',
        'vettore' => 'vettore',
        'capo_area' => 'capoArea',
        'persona_giuridica' => 'personaGiuridica',
        'banca' => 'banca',
        'nominativo1' => 'nominativo1',
        'nominativo2' => 'nominativo2',
        'sede_legale' => 'sedeLegale',
        'codice' => 'codice',
        'data_inserimento' => 'dataInserimento',
        'ragione_sociale1' => 'ragioneSociale1',
        'ragione_sociale2' => 'ragioneSociale2',
        'e_mail' => 'eMail',
        'sito_web' => 'sitoWeb',
        'partita_iva' => 'partitaIva',
        'codice_fiscale' => 'codiceFiscale',
        'note' => 'note',
        'note_importanti' => 'noteImportanti',
        'pec' => 'pec',
        'codice_destinatario' => 'codiceDestinatario',
        'dichiarazione_intento' => 'dichiarazioneIntento',
        'riferimento_amministrazione' => 'riferimentoAmministrazione'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'modello_id' => 'setModelloId',
        'nazione_id' => 'setNazioneId',
        'nazione' => 'setNazione',
        'area_geografica_id' => 'setAreaGeograficaId',
        'area_geografica' => 'setAreaGeografica',
        'valuta_id' => 'setValutaId',
        'valuta' => 'setValuta',
        'indirizzo_predefinito_id' => 'setIndirizzoPredefinitoId',
        'indirizzi' => 'setIndirizzi',
        'scheda_cliente' => 'setSchedaCliente',
        'scheda_fornitore' => 'setSchedaFornitore',
        'stato' => 'setStato',
        'cliente' => 'setCliente',
        'fornitore' => 'setFornitore',
        'agente' => 'setAgente',
        'vettore' => 'setVettore',
        'capo_area' => 'setCapoArea',
        'persona_giuridica' => 'setPersonaGiuridica',
        'banca' => 'setBanca',
        'nominativo1' => 'setNominativo1',
        'nominativo2' => 'setNominativo2',
        'sede_legale' => 'setSedeLegale',
        'codice' => 'setCodice',
        'data_inserimento' => 'setDataInserimento',
        'ragione_sociale1' => 'setRagioneSociale1',
        'ragione_sociale2' => 'setRagioneSociale2',
        'e_mail' => 'setEMail',
        'sito_web' => 'setSitoWeb',
        'partita_iva' => 'setPartitaIva',
        'codice_fiscale' => 'setCodiceFiscale',
        'note' => 'setNote',
        'note_importanti' => 'setNoteImportanti',
        'pec' => 'setPec',
        'codice_destinatario' => 'setCodiceDestinatario',
        'dichiarazione_intento' => 'setDichiarazioneIntento',
        'riferimento_amministrazione' => 'setRiferimentoAmministrazione'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'modello_id' => 'getModelloId',
        'nazione_id' => 'getNazioneId',
        'nazione' => 'getNazione',
        'area_geografica_id' => 'getAreaGeograficaId',
        'area_geografica' => 'getAreaGeografica',
        'valuta_id' => 'getValutaId',
        'valuta' => 'getValuta',
        'indirizzo_predefinito_id' => 'getIndirizzoPredefinitoId',
        'indirizzi' => 'getIndirizzi',
        'scheda_cliente' => 'getSchedaCliente',
        'scheda_fornitore' => 'getSchedaFornitore',
        'stato' => 'getStato',
        'cliente' => 'getCliente',
        'fornitore' => 'getFornitore',
        'agente' => 'getAgente',
        'vettore' => 'getVettore',
        'capo_area' => 'getCapoArea',
        'persona_giuridica' => 'getPersonaGiuridica',
        'banca' => 'getBanca',
        'nominativo1' => 'getNominativo1',
        'nominativo2' => 'getNominativo2',
        'sede_legale' => 'getSedeLegale',
        'codice' => 'getCodice',
        'data_inserimento' => 'getDataInserimento',
        'ragione_sociale1' => 'getRagioneSociale1',
        'ragione_sociale2' => 'getRagioneSociale2',
        'e_mail' => 'getEMail',
        'sito_web' => 'getSitoWeb',
        'partita_iva' => 'getPartitaIva',
        'codice_fiscale' => 'getCodiceFiscale',
        'note' => 'getNote',
        'note_importanti' => 'getNoteImportanti',
        'pec' => 'getPec',
        'codice_destinatario' => 'getCodiceDestinatario',
        'dichiarazione_intento' => 'getDichiarazioneIntento',
        'riferimento_amministrazione' => 'getRiferimentoAmministrazione'
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
        $this->setIfExists('modello_id', $data ?? [], null);
        $this->setIfExists('nazione_id', $data ?? [], null);
        $this->setIfExists('nazione', $data ?? [], null);
        $this->setIfExists('area_geografica_id', $data ?? [], null);
        $this->setIfExists('area_geografica', $data ?? [], null);
        $this->setIfExists('valuta_id', $data ?? [], null);
        $this->setIfExists('valuta', $data ?? [], null);
        $this->setIfExists('indirizzo_predefinito_id', $data ?? [], null);
        $this->setIfExists('indirizzi', $data ?? [], null);
        $this->setIfExists('scheda_cliente', $data ?? [], null);
        $this->setIfExists('scheda_fornitore', $data ?? [], null);
        $this->setIfExists('stato', $data ?? [], null);
        $this->setIfExists('cliente', $data ?? [], null);
        $this->setIfExists('fornitore', $data ?? [], null);
        $this->setIfExists('agente', $data ?? [], null);
        $this->setIfExists('vettore', $data ?? [], null);
        $this->setIfExists('capo_area', $data ?? [], null);
        $this->setIfExists('persona_giuridica', $data ?? [], null);
        $this->setIfExists('banca', $data ?? [], null);
        $this->setIfExists('nominativo1', $data ?? [], null);
        $this->setIfExists('nominativo2', $data ?? [], null);
        $this->setIfExists('sede_legale', $data ?? [], null);
        $this->setIfExists('codice', $data ?? [], null);
        $this->setIfExists('data_inserimento', $data ?? [], null);
        $this->setIfExists('ragione_sociale1', $data ?? [], null);
        $this->setIfExists('ragione_sociale2', $data ?? [], null);
        $this->setIfExists('e_mail', $data ?? [], null);
        $this->setIfExists('sito_web', $data ?? [], null);
        $this->setIfExists('partita_iva', $data ?? [], null);
        $this->setIfExists('codice_fiscale', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
        $this->setIfExists('note_importanti', $data ?? [], null);
        $this->setIfExists('pec', $data ?? [], null);
        $this->setIfExists('codice_destinatario', $data ?? [], null);
        $this->setIfExists('dichiarazione_intento', $data ?? [], null);
        $this->setIfExists('riferimento_amministrazione', $data ?? [], null);
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

        if (!is_null($this->container['codice']) && (mb_strlen($this->container['codice']) > 10)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['codice']) && (mb_strlen($this->container['codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be bigger than or equal to 0.";
        }

        if ($this->container['ragione_sociale1'] === null) {
            $invalidProperties[] = "'ragione_sociale1' can't be null";
        }
        if ((mb_strlen($this->container['ragione_sociale1']) > 60)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale1', the character length must be smaller than or equal to 60.";
        }

        if ((mb_strlen($this->container['ragione_sociale1']) < 0)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['ragione_sociale2']) && (mb_strlen($this->container['ragione_sociale2']) > 60)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale2', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['ragione_sociale2']) && (mb_strlen($this->container['ragione_sociale2']) < 0)) {
            $invalidProperties[] = "invalid value for 'ragione_sociale2', the character length must be bigger than or equal to 0.";
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

        if (!is_null($this->container['partita_iva']) && (mb_strlen($this->container['partita_iva']) > 30)) {
            $invalidProperties[] = "invalid value for 'partita_iva', the character length must be smaller than or equal to 30.";
        }

        if (!is_null($this->container['partita_iva']) && (mb_strlen($this->container['partita_iva']) < 0)) {
            $invalidProperties[] = "invalid value for 'partita_iva', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_fiscale']) && (mb_strlen($this->container['codice_fiscale']) > 20)) {
            $invalidProperties[] = "invalid value for 'codice_fiscale', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['codice_fiscale']) && (mb_strlen($this->container['codice_fiscale']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_fiscale', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['pec']) && (mb_strlen($this->container['pec']) > 120)) {
            $invalidProperties[] = "invalid value for 'pec', the character length must be smaller than or equal to 120.";
        }

        if (!is_null($this->container['pec']) && (mb_strlen($this->container['pec']) < 0)) {
            $invalidProperties[] = "invalid value for 'pec', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_destinatario']) && (mb_strlen($this->container['codice_destinatario']) > 7)) {
            $invalidProperties[] = "invalid value for 'codice_destinatario', the character length must be smaller than or equal to 7.";
        }

        if (!is_null($this->container['codice_destinatario']) && (mb_strlen($this->container['codice_destinatario']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_destinatario', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['dichiarazione_intento']) && (mb_strlen($this->container['dichiarazione_intento']) > 200)) {
            $invalidProperties[] = "invalid value for 'dichiarazione_intento', the character length must be smaller than or equal to 200.";
        }

        if (!is_null($this->container['dichiarazione_intento']) && (mb_strlen($this->container['dichiarazione_intento']) < 0)) {
            $invalidProperties[] = "invalid value for 'dichiarazione_intento', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['riferimento_amministrazione']) && (mb_strlen($this->container['riferimento_amministrazione']) > 20)) {
            $invalidProperties[] = "invalid value for 'riferimento_amministrazione', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['riferimento_amministrazione']) && (mb_strlen($this->container['riferimento_amministrazione']) < 0)) {
            $invalidProperties[] = "invalid value for 'riferimento_amministrazione', the character length must be bigger than or equal to 0.";
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
     * Gets modello_id
     *
     * @return int|null
     */
    public function getModelloId()
    {
        return $this->container['modello_id'];
    }

    /**
     * Sets modello_id
     *
     * @param int|null $modello_id Identificativo univoco di `Modello`. Sempre null. Questa proprietà è usata solo dal client desktop Amica 20 per Windows ed è da considerarsi obsoleta.
     *
     * @return self
     */
    public function setModelloId($modello_id)
    {
        if (is_null($modello_id)) {
            array_push($this->openAPINullablesSetToNull, 'modello_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('modello_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['modello_id'] = $modello_id;

        return $this;
    }

    /**
     * Gets nazione_id
     *
     * @return int|null
     */
    public function getNazioneId()
    {
        return $this->container['nazione_id'];
    }

    /**
     * Sets nazione_id
     *
     * @param int|null $nazione_id Identificativo univoco di `Nazione` (nazione).
     *
     * @return self
     */
    public function setNazioneId($nazione_id)
    {
        if (is_null($nazione_id)) {
            array_push($this->openAPINullablesSetToNull, 'nazione_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('nazione_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['nazione_id'] = $nazione_id;

        return $this;
    }

    /**
     * Gets nazione
     *
     * @return \Amica\Sdk\Model\Nazione|null
     */
    public function getNazione()
    {
        return $this->container['nazione'];
    }

    /**
     * Sets nazione
     *
     * @param \Amica\Sdk\Model\Nazione|null $nazione nazione
     *
     * @return self
     */
    public function setNazione($nazione)
    {
        if (is_null($nazione)) {
            throw new \InvalidArgumentException('non-nullable nazione cannot be null');
        }
        $this->container['nazione'] = $nazione;

        return $this;
    }

    /**
     * Gets area_geografica_id
     *
     * @return int|null
     */
    public function getAreaGeograficaId()
    {
        return $this->container['area_geografica_id'];
    }

    /**
     * Sets area_geografica_id
     *
     * @param int|null $area_geografica_id Identificativo univoco di `AreaGeografica` (area geografica).
     *
     * @return self
     */
    public function setAreaGeograficaId($area_geografica_id)
    {
        if (is_null($area_geografica_id)) {
            array_push($this->openAPINullablesSetToNull, 'area_geografica_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('area_geografica_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['area_geografica_id'] = $area_geografica_id;

        return $this;
    }

    /**
     * Gets area_geografica
     *
     * @return \Amica\Sdk\Model\AreaGeografica|null
     */
    public function getAreaGeografica()
    {
        return $this->container['area_geografica'];
    }

    /**
     * Sets area_geografica
     *
     * @param \Amica\Sdk\Model\AreaGeografica|null $area_geografica area_geografica
     *
     * @return self
     */
    public function setAreaGeografica($area_geografica)
    {
        if (is_null($area_geografica)) {
            throw new \InvalidArgumentException('non-nullable area_geografica cannot be null');
        }
        $this->container['area_geografica'] = $area_geografica;

        return $this;
    }

    /**
     * Gets valuta_id
     *
     * @return int|null
     */
    public function getValutaId()
    {
        return $this->container['valuta_id'];
    }

    /**
     * Sets valuta_id
     *
     * @param int|null $valuta_id Identificativo univoco di `Valuta` (valuta).
     *
     * @return self
     */
    public function setValutaId($valuta_id)
    {
        if (is_null($valuta_id)) {
            array_push($this->openAPINullablesSetToNull, 'valuta_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('valuta_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['valuta_id'] = $valuta_id;

        return $this;
    }

    /**
     * Gets valuta
     *
     * @return \Amica\Sdk\Model\Valuta|null
     */
    public function getValuta()
    {
        return $this->container['valuta'];
    }

    /**
     * Sets valuta
     *
     * @param \Amica\Sdk\Model\Valuta|null $valuta valuta
     *
     * @return self
     */
    public function setValuta($valuta)
    {
        if (is_null($valuta)) {
            throw new \InvalidArgumentException('non-nullable valuta cannot be null');
        }
        $this->container['valuta'] = $valuta;

        return $this;
    }

    /**
     * Gets indirizzo_predefinito_id
     *
     * @return int|null
     */
    public function getIndirizzoPredefinitoId()
    {
        return $this->container['indirizzo_predefinito_id'];
    }

    /**
     * Sets indirizzo_predefinito_id
     *
     * @param int|null $indirizzo_predefinito_id Identificativo univoco dell'`Indirizzo` (indirizzo) predefinito.
     *
     * @return self
     */
    public function setIndirizzoPredefinitoId($indirizzo_predefinito_id)
    {
        if (is_null($indirizzo_predefinito_id)) {
            array_push($this->openAPINullablesSetToNull, 'indirizzo_predefinito_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('indirizzo_predefinito_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['indirizzo_predefinito_id'] = $indirizzo_predefinito_id;

        return $this;
    }

    /**
     * Gets indirizzi
     *
     * @return \Amica\Sdk\Model\Indirizzo[]|null
     */
    public function getIndirizzi()
    {
        return $this->container['indirizzi'];
    }

    /**
     * Sets indirizzi
     *
     * @param \Amica\Sdk\Model\Indirizzo[]|null $indirizzi Elenco degli `Indirizzo` dell'anagrafica.
     *
     * @return self
     */
    public function setIndirizzi($indirizzi)
    {
        if (is_null($indirizzi)) {
            array_push($this->openAPINullablesSetToNull, 'indirizzi');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('indirizzi', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['indirizzi'] = $indirizzi;

        return $this;
    }

    /**
     * Gets scheda_cliente
     *
     * @return \Amica\Sdk\Model\SchedaCliente|null
     */
    public function getSchedaCliente()
    {
        return $this->container['scheda_cliente'];
    }

    /**
     * Sets scheda_cliente
     *
     * @param \Amica\Sdk\Model\SchedaCliente|null $scheda_cliente scheda_cliente
     *
     * @return self
     */
    public function setSchedaCliente($scheda_cliente)
    {
        if (is_null($scheda_cliente)) {
            throw new \InvalidArgumentException('non-nullable scheda_cliente cannot be null');
        }
        $this->container['scheda_cliente'] = $scheda_cliente;

        return $this;
    }

    /**
     * Gets scheda_fornitore
     *
     * @return \Amica\Sdk\Model\SchedaFornitore|null
     */
    public function getSchedaFornitore()
    {
        return $this->container['scheda_fornitore'];
    }

    /**
     * Sets scheda_fornitore
     *
     * @param \Amica\Sdk\Model\SchedaFornitore|null $scheda_fornitore scheda_fornitore
     *
     * @return self
     */
    public function setSchedaFornitore($scheda_fornitore)
    {
        if (is_null($scheda_fornitore)) {
            throw new \InvalidArgumentException('non-nullable scheda_fornitore cannot be null');
        }
        $this->container['scheda_fornitore'] = $scheda_fornitore;

        return $this;
    }

    /**
     * Gets stato
     *
     * @return bool|null
     */
    public function getStato()
    {
        return $this->container['stato'];
    }

    /**
     * Sets stato
     *
     * @param bool|null $stato Indica se l'anagrafica è attiva o meno.
     *
     * @return self
     */
    public function setStato($stato)
    {
        if (is_null($stato)) {
            throw new \InvalidArgumentException('non-nullable stato cannot be null');
        }
        $this->container['stato'] = $stato;

        return $this;
    }

    /**
     * Gets cliente
     *
     * @return bool|null
     */
    public function getCliente()
    {
        return $this->container['cliente'];
    }

    /**
     * Sets cliente
     *
     * @param bool|null $cliente Indica se l'anagrafica è un cliente.
     *
     * @return self
     */
    public function setCliente($cliente)
    {
        if (is_null($cliente)) {
            throw new \InvalidArgumentException('non-nullable cliente cannot be null');
        }
        $this->container['cliente'] = $cliente;

        return $this;
    }

    /**
     * Gets fornitore
     *
     * @return bool|null
     */
    public function getFornitore()
    {
        return $this->container['fornitore'];
    }

    /**
     * Sets fornitore
     *
     * @param bool|null $fornitore Indica se l'anagrafica è un fornitore.
     *
     * @return self
     */
    public function setFornitore($fornitore)
    {
        if (is_null($fornitore)) {
            throw new \InvalidArgumentException('non-nullable fornitore cannot be null');
        }
        $this->container['fornitore'] = $fornitore;

        return $this;
    }

    /**
     * Gets agente
     *
     * @return bool|null
     */
    public function getAgente()
    {
        return $this->container['agente'];
    }

    /**
     * Sets agente
     *
     * @param bool|null $agente Indica se l'anagrafica opera come agente di vendita per conto dell'azienda.
     *
     * @return self
     */
    public function setAgente($agente)
    {
        if (is_null($agente)) {
            throw new \InvalidArgumentException('non-nullable agente cannot be null');
        }
        $this->container['agente'] = $agente;

        return $this;
    }

    /**
     * Gets vettore
     *
     * @return bool|null
     */
    public function getVettore()
    {
        return $this->container['vettore'];
    }

    /**
     * Sets vettore
     *
     * @param bool|null $vettore Indica se l'anagrafica opera come vettore.
     *
     * @return self
     */
    public function setVettore($vettore)
    {
        if (is_null($vettore)) {
            throw new \InvalidArgumentException('non-nullable vettore cannot be null');
        }
        $this->container['vettore'] = $vettore;

        return $this;
    }

    /**
     * Gets capo_area
     *
     * @return bool|null
     */
    public function getCapoArea()
    {
        return $this->container['capo_area'];
    }

    /**
     * Sets capo_area
     *
     * @param bool|null $capo_area Indica se l'anagrafica opera come capo area.
     *
     * @return self
     */
    public function setCapoArea($capo_area)
    {
        if (is_null($capo_area)) {
            throw new \InvalidArgumentException('non-nullable capo_area cannot be null');
        }
        $this->container['capo_area'] = $capo_area;

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
     * @param bool|null $persona_giuridica Indica se l'anagrafica è una persona giuridica o meno. `true` indica una persona giuridica, `false` una persona fisica.
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
     * Gets banca
     *
     * @return \Amica\Sdk\Model\Banca|null
     */
    public function getBanca()
    {
        return $this->container['banca'];
    }

    /**
     * Sets banca
     *
     * @param \Amica\Sdk\Model\Banca|null $banca banca
     *
     * @return self
     */
    public function setBanca($banca)
    {
        if (is_null($banca)) {
            throw new \InvalidArgumentException('non-nullable banca cannot be null');
        }
        $this->container['banca'] = $banca;

        return $this;
    }

    /**
     * Gets nominativo1
     *
     * @return \Amica\Sdk\Model\AnagraficaNominativo1|null
     */
    public function getNominativo1()
    {
        return $this->container['nominativo1'];
    }

    /**
     * Sets nominativo1
     *
     * @param \Amica\Sdk\Model\AnagraficaNominativo1|null $nominativo1 nominativo1
     *
     * @return self
     */
    public function setNominativo1($nominativo1)
    {
        if (is_null($nominativo1)) {
            throw new \InvalidArgumentException('non-nullable nominativo1 cannot be null');
        }
        $this->container['nominativo1'] = $nominativo1;

        return $this;
    }

    /**
     * Gets nominativo2
     *
     * @return \Amica\Sdk\Model\AnagraficaNominativo2|null
     */
    public function getNominativo2()
    {
        return $this->container['nominativo2'];
    }

    /**
     * Sets nominativo2
     *
     * @param \Amica\Sdk\Model\AnagraficaNominativo2|null $nominativo2 nominativo2
     *
     * @return self
     */
    public function setNominativo2($nominativo2)
    {
        if (is_null($nominativo2)) {
            throw new \InvalidArgumentException('non-nullable nominativo2 cannot be null');
        }
        $this->container['nominativo2'] = $nominativo2;

        return $this;
    }

    /**
     * Gets sede_legale
     *
     * @return \Amica\Sdk\Model\AnagraficaSedeLegale|null
     */
    public function getSedeLegale()
    {
        return $this->container['sede_legale'];
    }

    /**
     * Sets sede_legale
     *
     * @param \Amica\Sdk\Model\AnagraficaSedeLegale|null $sede_legale sede_legale
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
     * Gets codice
     *
     * @return string|null
     */
    public function getCodice()
    {
        return $this->container['codice'];
    }

    /**
     * Sets codice
     *
     * @param string|null $codice Codice assegnato all'anagrafica.
     *
     * @return self
     */
    public function setCodice($codice)
    {
        if (is_null($codice)) {
            array_push($this->openAPINullablesSetToNull, 'codice');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice) && (mb_strlen($codice) > 10)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling Anagrafica., must be smaller than or equal to 10.');
        }
        if (!is_null($codice) && (mb_strlen($codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['codice'] = $codice;

        return $this;
    }

    /**
     * Gets data_inserimento
     *
     * @return \DateTime|null
     */
    public function getDataInserimento()
    {
        return $this->container['data_inserimento'];
    }

    /**
     * Sets data_inserimento
     *
     * @param \DateTime|null $data_inserimento Data di primo inserimento.
     *
     * @return self
     */
    public function setDataInserimento($data_inserimento)
    {
        if (is_null($data_inserimento)) {
            array_push($this->openAPINullablesSetToNull, 'data_inserimento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_inserimento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_inserimento'] = $data_inserimento;

        return $this;
    }

    /**
     * Gets ragione_sociale1
     *
     * @return string
     */
    public function getRagioneSociale1()
    {
        return $this->container['ragione_sociale1'];
    }

    /**
     * Sets ragione_sociale1
     *
     * @param string $ragione_sociale1 Ragione sociale, oppure nome e cognome della persona fisica.
     *
     * @return self
     */
    public function setRagioneSociale1($ragione_sociale1)
    {
        if (is_null($ragione_sociale1)) {
            throw new \InvalidArgumentException('non-nullable ragione_sociale1 cannot be null');
        }
        if ((mb_strlen($ragione_sociale1) > 60)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale1 when calling Anagrafica., must be smaller than or equal to 60.');
        }
        if ((mb_strlen($ragione_sociale1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale1 when calling Anagrafica., must be bigger than or equal to 0.');
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
     * @param string|null $ragione_sociale2 Eventuale seconda riga della ragione sociale. Usata principalmente nelle stampe, quando si vuole suddividere la ragione sociale su due righe.
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
            throw new \InvalidArgumentException('invalid length for $ragione_sociale2 when calling Anagrafica., must be smaller than or equal to 60.');
        }
        if (!is_null($ragione_sociale2) && (mb_strlen($ragione_sociale2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $ragione_sociale2 when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['ragione_sociale2'] = $ragione_sociale2;

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
     * @param string|null $e_mail Indirizzo email.
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
            throw new \InvalidArgumentException('invalid length for $e_mail when calling Anagrafica., must be smaller than or equal to 50.');
        }
        if (!is_null($e_mail) && (mb_strlen($e_mail) < 0)) {
            throw new \InvalidArgumentException('invalid length for $e_mail when calling Anagrafica., must be bigger than or equal to 0.');
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
     * @param string|null $sito_web Indirizzo web.
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
            throw new \InvalidArgumentException('invalid length for $sito_web when calling Anagrafica., must be smaller than or equal to 50.');
        }
        if (!is_null($sito_web) && (mb_strlen($sito_web) < 0)) {
            throw new \InvalidArgumentException('invalid length for $sito_web when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['sito_web'] = $sito_web;

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
     * @param string|null $partita_iva Partita IVA. La correttezza formale è verificata dal servizio al salvataggio: l'errore che ne deriva è forzabile con `validationOverride`.
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
        if (!is_null($partita_iva) && (mb_strlen($partita_iva) > 30)) {
            throw new \InvalidArgumentException('invalid length for $partita_iva when calling Anagrafica., must be smaller than or equal to 30.');
        }
        if (!is_null($partita_iva) && (mb_strlen($partita_iva) < 0)) {
            throw new \InvalidArgumentException('invalid length for $partita_iva when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['partita_iva'] = $partita_iva;

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
     * @param string|null $codice_fiscale Codice fiscale. Solitamente diverso dalla `PartitaIva`. Come per la partita IVA, la correttezza formale è verificata dal servizio al salvataggio e l'errore è forzabile con `validationOverride`.
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
        if (!is_null($codice_fiscale) && (mb_strlen($codice_fiscale) > 20)) {
            throw new \InvalidArgumentException('invalid length for $codice_fiscale when calling Anagrafica., must be smaller than or equal to 20.');
        }
        if (!is_null($codice_fiscale) && (mb_strlen($codice_fiscale) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_fiscale when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['codice_fiscale'] = $codice_fiscale;

        return $this;
    }

    /**
     * Gets note
     *
     * @return string|null
     */
    public function getNote()
    {
        return $this->container['note'];
    }

    /**
     * Sets note
     *
     * @param string|null $note Note.
     *
     * @return self
     */
    public function setNote($note)
    {
        if (is_null($note)) {
            array_push($this->openAPINullablesSetToNull, 'note');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('note', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['note'] = $note;

        return $this;
    }

    /**
     * Gets note_importanti
     *
     * @return string|null
     */
    public function getNoteImportanti()
    {
        return $this->container['note_importanti'];
    }

    /**
     * Sets note_importanti
     *
     * @param string|null $note_importanti Note rilevanti. I client ufficiali Amica le evidenziano alla creazione di un nuovo documento fiscale.
     *
     * @return self
     */
    public function setNoteImportanti($note_importanti)
    {
        if (is_null($note_importanti)) {
            array_push($this->openAPINullablesSetToNull, 'note_importanti');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('note_importanti', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['note_importanti'] = $note_importanti;

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
     * @param string|null $pec Indirizzo Pec (posta elettronica certificata).
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
            throw new \InvalidArgumentException('invalid length for $pec when calling Anagrafica., must be smaller than or equal to 120.');
        }
        if (!is_null($pec) && (mb_strlen($pec) < 0)) {
            throw new \InvalidArgumentException('invalid length for $pec when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['pec'] = $pec;

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
     * @param string|null $codice_destinatario Codice destinatario per il sistema fiscale italiano. Usato per il recapito delle fatture elettroniche italiane.
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
            throw new \InvalidArgumentException('invalid length for $codice_destinatario when calling Anagrafica., must be smaller than or equal to 7.');
        }
        if (!is_null($codice_destinatario) && (mb_strlen($codice_destinatario) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_destinatario when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['codice_destinatario'] = $codice_destinatario;

        return $this;
    }

    /**
     * Gets dichiarazione_intento
     *
     * @return string|null
     */
    public function getDichiarazioneIntento()
    {
        return $this->container['dichiarazione_intento'];
    }

    /**
     * Sets dichiarazione_intento
     *
     * @param string|null $dichiarazione_intento Numero di protocollo della dichiarazione d'intento.
     *
     * @return self
     */
    public function setDichiarazioneIntento($dichiarazione_intento)
    {
        if (is_null($dichiarazione_intento)) {
            array_push($this->openAPINullablesSetToNull, 'dichiarazione_intento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('dichiarazione_intento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($dichiarazione_intento) && (mb_strlen($dichiarazione_intento) > 200)) {
            throw new \InvalidArgumentException('invalid length for $dichiarazione_intento when calling Anagrafica., must be smaller than or equal to 200.');
        }
        if (!is_null($dichiarazione_intento) && (mb_strlen($dichiarazione_intento) < 0)) {
            throw new \InvalidArgumentException('invalid length for $dichiarazione_intento when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['dichiarazione_intento'] = $dichiarazione_intento;

        return $this;
    }

    /**
     * Gets riferimento_amministrazione
     *
     * @return string|null
     */
    public function getRiferimentoAmministrazione()
    {
        return $this->container['riferimento_amministrazione'];
    }

    /**
     * Sets riferimento_amministrazione
     *
     * @param string|null $riferimento_amministrazione Solitamente necessario quando si ha a che fare con enti della Pubblica Amministrazione italiana. Identifica univocamente l'ufficio di destinazione all'interno della PA.
     *
     * @return self
     */
    public function setRiferimentoAmministrazione($riferimento_amministrazione)
    {
        if (is_null($riferimento_amministrazione)) {
            array_push($this->openAPINullablesSetToNull, 'riferimento_amministrazione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('riferimento_amministrazione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($riferimento_amministrazione) && (mb_strlen($riferimento_amministrazione) > 20)) {
            throw new \InvalidArgumentException('invalid length for $riferimento_amministrazione when calling Anagrafica., must be smaller than or equal to 20.');
        }
        if (!is_null($riferimento_amministrazione) && (mb_strlen($riferimento_amministrazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $riferimento_amministrazione when calling Anagrafica., must be bigger than or equal to 0.');
        }

        $this->container['riferimento_amministrazione'] = $riferimento_amministrazione;

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


