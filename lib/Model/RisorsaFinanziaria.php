<?php
/**
 * RisorsaFinanziaria
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
 * The version of the OpenAPI document: 3.2.1
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
 * RisorsaFinanziaria Class Doc Comment
 *
 * @category Class
 * @description Risorsa finanziaria.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class RisorsaFinanziaria implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'RisorsaFinanziaria';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'nome' => 'string',
        'indirizzo' => 'string',
        'localit' => 'string',
        'cap' => 'string',
        'provincia' => 'string',
        'telefono1' => 'string',
        'telefono2' => 'string',
        'fax' => 'string',
        'contatto1_nome' => 'string',
        'contatto1_incarico' => 'string',
        'contatto1_telefono1' => 'string',
        'contatto1_telefono2' => 'string',
        'contatto1_e_mail' => 'string',
        'contatto2_nome' => 'string',
        'contatto2_incarico' => 'string',
        'contatto2_telefono1' => 'string',
        'contatto2_telefono2' => 'string',
        'contatto2_e_mail' => 'string',
        'codice_abi' => 'string',
        'codice_cab' => 'string',
        'conto_corrente' => 'string',
        'codice_cin' => 'string',
        'codice_cin_eur' => 'string',
        'codice_cin_paese' => 'string',
        'codice_swift' => 'string',
        'codice_iban' => 'string',
        'web' => 'string',
        'tipologia' => 'string'
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
        'nome' => null,
        'indirizzo' => null,
        'localit' => null,
        'cap' => null,
        'provincia' => null,
        'telefono1' => null,
        'telefono2' => null,
        'fax' => null,
        'contatto1_nome' => null,
        'contatto1_incarico' => null,
        'contatto1_telefono1' => null,
        'contatto1_telefono2' => null,
        'contatto1_e_mail' => null,
        'contatto2_nome' => null,
        'contatto2_incarico' => null,
        'contatto2_telefono1' => null,
        'contatto2_telefono2' => null,
        'contatto2_e_mail' => null,
        'codice_abi' => null,
        'codice_cab' => null,
        'conto_corrente' => null,
        'codice_cin' => null,
        'codice_cin_eur' => null,
        'codice_cin_paese' => null,
        'codice_swift' => null,
        'codice_iban' => null,
        'web' => null,
        'tipologia' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'nome' => false,
        'indirizzo' => true,
        'localit' => true,
        'cap' => true,
        'provincia' => true,
        'telefono1' => true,
        'telefono2' => true,
        'fax' => true,
        'contatto1_nome' => true,
        'contatto1_incarico' => true,
        'contatto1_telefono1' => true,
        'contatto1_telefono2' => true,
        'contatto1_e_mail' => true,
        'contatto2_nome' => true,
        'contatto2_incarico' => true,
        'contatto2_telefono1' => true,
        'contatto2_telefono2' => true,
        'contatto2_e_mail' => true,
        'codice_abi' => true,
        'codice_cab' => true,
        'conto_corrente' => true,
        'codice_cin' => true,
        'codice_cin_eur' => true,
        'codice_cin_paese' => true,
        'codice_swift' => true,
        'codice_iban' => true,
        'web' => true,
        'tipologia' => false
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
        'nome' => 'nome',
        'indirizzo' => 'indirizzo',
        'localit' => 'località',
        'cap' => 'cap',
        'provincia' => 'provincia',
        'telefono1' => 'telefono1',
        'telefono2' => 'telefono2',
        'fax' => 'fax',
        'contatto1_nome' => 'contatto1Nome',
        'contatto1_incarico' => 'contatto1Incarico',
        'contatto1_telefono1' => 'contatto1Telefono1',
        'contatto1_telefono2' => 'contatto1Telefono2',
        'contatto1_e_mail' => 'contatto1EMail',
        'contatto2_nome' => 'contatto2Nome',
        'contatto2_incarico' => 'contatto2Incarico',
        'contatto2_telefono1' => 'contatto2Telefono1',
        'contatto2_telefono2' => 'contatto2Telefono2',
        'contatto2_e_mail' => 'contatto2EMail',
        'codice_abi' => 'codiceAbi',
        'codice_cab' => 'codiceCab',
        'conto_corrente' => 'contoCorrente',
        'codice_cin' => 'codiceCin',
        'codice_cin_eur' => 'codiceCinEur',
        'codice_cin_paese' => 'codiceCinPaese',
        'codice_swift' => 'codiceSwift',
        'codice_iban' => 'codiceIban',
        'web' => 'web',
        'tipologia' => 'tipologia'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'nome' => 'setNome',
        'indirizzo' => 'setIndirizzo',
        'localit' => 'setLocalit',
        'cap' => 'setCap',
        'provincia' => 'setProvincia',
        'telefono1' => 'setTelefono1',
        'telefono2' => 'setTelefono2',
        'fax' => 'setFax',
        'contatto1_nome' => 'setContatto1Nome',
        'contatto1_incarico' => 'setContatto1Incarico',
        'contatto1_telefono1' => 'setContatto1Telefono1',
        'contatto1_telefono2' => 'setContatto1Telefono2',
        'contatto1_e_mail' => 'setContatto1EMail',
        'contatto2_nome' => 'setContatto2Nome',
        'contatto2_incarico' => 'setContatto2Incarico',
        'contatto2_telefono1' => 'setContatto2Telefono1',
        'contatto2_telefono2' => 'setContatto2Telefono2',
        'contatto2_e_mail' => 'setContatto2EMail',
        'codice_abi' => 'setCodiceAbi',
        'codice_cab' => 'setCodiceCab',
        'conto_corrente' => 'setContoCorrente',
        'codice_cin' => 'setCodiceCin',
        'codice_cin_eur' => 'setCodiceCinEur',
        'codice_cin_paese' => 'setCodiceCinPaese',
        'codice_swift' => 'setCodiceSwift',
        'codice_iban' => 'setCodiceIban',
        'web' => 'setWeb',
        'tipologia' => 'setTipologia'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'nome' => 'getNome',
        'indirizzo' => 'getIndirizzo',
        'localit' => 'getLocalit',
        'cap' => 'getCap',
        'provincia' => 'getProvincia',
        'telefono1' => 'getTelefono1',
        'telefono2' => 'getTelefono2',
        'fax' => 'getFax',
        'contatto1_nome' => 'getContatto1Nome',
        'contatto1_incarico' => 'getContatto1Incarico',
        'contatto1_telefono1' => 'getContatto1Telefono1',
        'contatto1_telefono2' => 'getContatto1Telefono2',
        'contatto1_e_mail' => 'getContatto1EMail',
        'contatto2_nome' => 'getContatto2Nome',
        'contatto2_incarico' => 'getContatto2Incarico',
        'contatto2_telefono1' => 'getContatto2Telefono1',
        'contatto2_telefono2' => 'getContatto2Telefono2',
        'contatto2_e_mail' => 'getContatto2EMail',
        'codice_abi' => 'getCodiceAbi',
        'codice_cab' => 'getCodiceCab',
        'conto_corrente' => 'getContoCorrente',
        'codice_cin' => 'getCodiceCin',
        'codice_cin_eur' => 'getCodiceCinEur',
        'codice_cin_paese' => 'getCodiceCinPaese',
        'codice_swift' => 'getCodiceSwift',
        'codice_iban' => 'getCodiceIban',
        'web' => 'getWeb',
        'tipologia' => 'getTipologia'
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

    public const TIPOLOGIA_BANCA = 'Banca';
    public const TIPOLOGIA_CASSA = 'Cassa';
    public const TIPOLOGIA_CARTA_CREDITO = 'CartaCredito';
    public const TIPOLOGIA_ALTRO = 'Altro';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipologiaAllowableValues()
    {
        return [
            self::TIPOLOGIA_BANCA,
            self::TIPOLOGIA_CASSA,
            self::TIPOLOGIA_CARTA_CREDITO,
            self::TIPOLOGIA_ALTRO,
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
        $this->setIfExists('nome', $data ?? [], null);
        $this->setIfExists('indirizzo', $data ?? [], null);
        $this->setIfExists('localit', $data ?? [], null);
        $this->setIfExists('cap', $data ?? [], null);
        $this->setIfExists('provincia', $data ?? [], null);
        $this->setIfExists('telefono1', $data ?? [], null);
        $this->setIfExists('telefono2', $data ?? [], null);
        $this->setIfExists('fax', $data ?? [], null);
        $this->setIfExists('contatto1_nome', $data ?? [], null);
        $this->setIfExists('contatto1_incarico', $data ?? [], null);
        $this->setIfExists('contatto1_telefono1', $data ?? [], null);
        $this->setIfExists('contatto1_telefono2', $data ?? [], null);
        $this->setIfExists('contatto1_e_mail', $data ?? [], null);
        $this->setIfExists('contatto2_nome', $data ?? [], null);
        $this->setIfExists('contatto2_incarico', $data ?? [], null);
        $this->setIfExists('contatto2_telefono1', $data ?? [], null);
        $this->setIfExists('contatto2_telefono2', $data ?? [], null);
        $this->setIfExists('contatto2_e_mail', $data ?? [], null);
        $this->setIfExists('codice_abi', $data ?? [], null);
        $this->setIfExists('codice_cab', $data ?? [], null);
        $this->setIfExists('conto_corrente', $data ?? [], null);
        $this->setIfExists('codice_cin', $data ?? [], null);
        $this->setIfExists('codice_cin_eur', $data ?? [], null);
        $this->setIfExists('codice_cin_paese', $data ?? [], null);
        $this->setIfExists('codice_swift', $data ?? [], null);
        $this->setIfExists('codice_iban', $data ?? [], null);
        $this->setIfExists('web', $data ?? [], null);
        $this->setIfExists('tipologia', $data ?? [], null);
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

        if ($this->container['nome'] === null) {
            $invalidProperties[] = "'nome' can't be null";
        }
        if ((mb_strlen($this->container['nome']) > 60)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be smaller than or equal to 60.";
        }

        if ((mb_strlen($this->container['nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['indirizzo']) && (mb_strlen($this->container['indirizzo']) > 60)) {
            $invalidProperties[] = "invalid value for 'indirizzo', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['indirizzo']) && (mb_strlen($this->container['indirizzo']) < 0)) {
            $invalidProperties[] = "invalid value for 'indirizzo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['localit']) && (mb_strlen($this->container['localit']) > 60)) {
            $invalidProperties[] = "invalid value for 'localit', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['localit']) && (mb_strlen($this->container['localit']) < 0)) {
            $invalidProperties[] = "invalid value for 'localit', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['cap']) && (mb_strlen($this->container['cap']) > 8)) {
            $invalidProperties[] = "invalid value for 'cap', the character length must be smaller than or equal to 8.";
        }

        if (!is_null($this->container['cap']) && (mb_strlen($this->container['cap']) < 0)) {
            $invalidProperties[] = "invalid value for 'cap', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['provincia']) && (mb_strlen($this->container['provincia']) > 3)) {
            $invalidProperties[] = "invalid value for 'provincia', the character length must be smaller than or equal to 3.";
        }

        if (!is_null($this->container['provincia']) && (mb_strlen($this->container['provincia']) < 0)) {
            $invalidProperties[] = "invalid value for 'provincia', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['telefono1']) && (mb_strlen($this->container['telefono1']) > 25)) {
            $invalidProperties[] = "invalid value for 'telefono1', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['telefono1']) && (mb_strlen($this->container['telefono1']) < 0)) {
            $invalidProperties[] = "invalid value for 'telefono1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['telefono2']) && (mb_strlen($this->container['telefono2']) > 25)) {
            $invalidProperties[] = "invalid value for 'telefono2', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['telefono2']) && (mb_strlen($this->container['telefono2']) < 0)) {
            $invalidProperties[] = "invalid value for 'telefono2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['fax']) && (mb_strlen($this->container['fax']) > 25)) {
            $invalidProperties[] = "invalid value for 'fax', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['fax']) && (mb_strlen($this->container['fax']) < 0)) {
            $invalidProperties[] = "invalid value for 'fax', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto1_nome']) && (mb_strlen($this->container['contatto1_nome']) > 60)) {
            $invalidProperties[] = "invalid value for 'contatto1_nome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['contatto1_nome']) && (mb_strlen($this->container['contatto1_nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto1_nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto1_incarico']) && (mb_strlen($this->container['contatto1_incarico']) > 40)) {
            $invalidProperties[] = "invalid value for 'contatto1_incarico', the character length must be smaller than or equal to 40.";
        }

        if (!is_null($this->container['contatto1_incarico']) && (mb_strlen($this->container['contatto1_incarico']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto1_incarico', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto1_telefono1']) && (mb_strlen($this->container['contatto1_telefono1']) > 25)) {
            $invalidProperties[] = "invalid value for 'contatto1_telefono1', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['contatto1_telefono1']) && (mb_strlen($this->container['contatto1_telefono1']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto1_telefono1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto1_telefono2']) && (mb_strlen($this->container['contatto1_telefono2']) > 25)) {
            $invalidProperties[] = "invalid value for 'contatto1_telefono2', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['contatto1_telefono2']) && (mb_strlen($this->container['contatto1_telefono2']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto1_telefono2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto1_e_mail']) && (mb_strlen($this->container['contatto1_e_mail']) > 50)) {
            $invalidProperties[] = "invalid value for 'contatto1_e_mail', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['contatto1_e_mail']) && (mb_strlen($this->container['contatto1_e_mail']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto1_e_mail', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto2_nome']) && (mb_strlen($this->container['contatto2_nome']) > 60)) {
            $invalidProperties[] = "invalid value for 'contatto2_nome', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['contatto2_nome']) && (mb_strlen($this->container['contatto2_nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto2_nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto2_incarico']) && (mb_strlen($this->container['contatto2_incarico']) > 40)) {
            $invalidProperties[] = "invalid value for 'contatto2_incarico', the character length must be smaller than or equal to 40.";
        }

        if (!is_null($this->container['contatto2_incarico']) && (mb_strlen($this->container['contatto2_incarico']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto2_incarico', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto2_telefono1']) && (mb_strlen($this->container['contatto2_telefono1']) > 25)) {
            $invalidProperties[] = "invalid value for 'contatto2_telefono1', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['contatto2_telefono1']) && (mb_strlen($this->container['contatto2_telefono1']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto2_telefono1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto2_telefono2']) && (mb_strlen($this->container['contatto2_telefono2']) > 25)) {
            $invalidProperties[] = "invalid value for 'contatto2_telefono2', the character length must be smaller than or equal to 25.";
        }

        if (!is_null($this->container['contatto2_telefono2']) && (mb_strlen($this->container['contatto2_telefono2']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto2_telefono2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['contatto2_e_mail']) && (mb_strlen($this->container['contatto2_e_mail']) > 50)) {
            $invalidProperties[] = "invalid value for 'contatto2_e_mail', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['contatto2_e_mail']) && (mb_strlen($this->container['contatto2_e_mail']) < 0)) {
            $invalidProperties[] = "invalid value for 'contatto2_e_mail', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_abi']) && (mb_strlen($this->container['codice_abi']) > 10)) {
            $invalidProperties[] = "invalid value for 'codice_abi', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['codice_abi']) && (mb_strlen($this->container['codice_abi']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_abi', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cab']) && (mb_strlen($this->container['codice_cab']) > 10)) {
            $invalidProperties[] = "invalid value for 'codice_cab', the character length must be smaller than or equal to 10.";
        }

        if (!is_null($this->container['codice_cab']) && (mb_strlen($this->container['codice_cab']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cab', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['conto_corrente']) && (mb_strlen($this->container['conto_corrente']) > 20)) {
            $invalidProperties[] = "invalid value for 'conto_corrente', the character length must be smaller than or equal to 20.";
        }

        if (!is_null($this->container['conto_corrente']) && (mb_strlen($this->container['conto_corrente']) < 0)) {
            $invalidProperties[] = "invalid value for 'conto_corrente', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cin']) && (mb_strlen($this->container['codice_cin']) > 1)) {
            $invalidProperties[] = "invalid value for 'codice_cin', the character length must be smaller than or equal to 1.";
        }

        if (!is_null($this->container['codice_cin']) && (mb_strlen($this->container['codice_cin']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cin', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cin_eur']) && (mb_strlen($this->container['codice_cin_eur']) > 2)) {
            $invalidProperties[] = "invalid value for 'codice_cin_eur', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['codice_cin_eur']) && (mb_strlen($this->container['codice_cin_eur']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cin_eur', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_cin_paese']) && (mb_strlen($this->container['codice_cin_paese']) > 2)) {
            $invalidProperties[] = "invalid value for 'codice_cin_paese', the character length must be smaller than or equal to 2.";
        }

        if (!is_null($this->container['codice_cin_paese']) && (mb_strlen($this->container['codice_cin_paese']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_cin_paese', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_swift']) && (mb_strlen($this->container['codice_swift']) > 15)) {
            $invalidProperties[] = "invalid value for 'codice_swift', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['codice_swift']) && (mb_strlen($this->container['codice_swift']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_swift', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['codice_iban']) && (mb_strlen($this->container['codice_iban']) > 34)) {
            $invalidProperties[] = "invalid value for 'codice_iban', the character length must be smaller than or equal to 34.";
        }

        if (!is_null($this->container['codice_iban']) && (mb_strlen($this->container['codice_iban']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice_iban', the character length must be bigger than or equal to 0.";
        }

        $allowedValues = $this->getTipologiaAllowableValues();
        if (!is_null($this->container['tipologia']) && !in_array($this->container['tipologia'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipologia', must be one of '%s'",
                $this->container['tipologia'],
                implode("', '", $allowedValues)
            );
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
     * Gets nome
     *
     * @return string
     */
    public function getNome()
    {
        return $this->container['nome'];
    }

    /**
     * Sets nome
     *
     * @param string $nome Nome o descrizione.
     *
     * @return self
     */
    public function setNome($nome)
    {
        if (is_null($nome)) {
            throw new \InvalidArgumentException('non-nullable nome cannot be null');
        }
        if ((mb_strlen($nome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling RisorsaFinanziaria., must be smaller than or equal to 60.');
        }
        if ((mb_strlen($nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['nome'] = $nome;

        return $this;
    }

    /**
     * Gets indirizzo
     *
     * @return string|null
     */
    public function getIndirizzo()
    {
        return $this->container['indirizzo'];
    }

    /**
     * Sets indirizzo
     *
     * @param string|null $indirizzo Indirizzo.
     *
     * @return self
     */
    public function setIndirizzo($indirizzo)
    {
        if (is_null($indirizzo)) {
            array_push($this->openAPINullablesSetToNull, 'indirizzo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('indirizzo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($indirizzo) && (mb_strlen($indirizzo) > 60)) {
            throw new \InvalidArgumentException('invalid length for $indirizzo when calling RisorsaFinanziaria., must be smaller than or equal to 60.');
        }
        if (!is_null($indirizzo) && (mb_strlen($indirizzo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $indirizzo when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['indirizzo'] = $indirizzo;

        return $this;
    }

    /**
     * Gets localit
     *
     * @return string|null
     */
    public function getLocalit()
    {
        return $this->container['localit'];
    }

    /**
     * Sets localit
     *
     * @param string|null $localit Città, comune o altra denominazione di località.
     *
     * @return self
     */
    public function setLocalit($localit)
    {
        if (is_null($localit)) {
            array_push($this->openAPINullablesSetToNull, 'localit');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('localit', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($localit) && (mb_strlen($localit) > 60)) {
            throw new \InvalidArgumentException('invalid length for $localit when calling RisorsaFinanziaria., must be smaller than or equal to 60.');
        }
        if (!is_null($localit) && (mb_strlen($localit) < 0)) {
            throw new \InvalidArgumentException('invalid length for $localit when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['localit'] = $localit;

        return $this;
    }

    /**
     * Gets cap
     *
     * @return string|null
     */
    public function getCap()
    {
        return $this->container['cap'];
    }

    /**
     * Sets cap
     *
     * @param string|null $cap CAP o codice di zona.
     *
     * @return self
     */
    public function setCap($cap)
    {
        if (is_null($cap)) {
            array_push($this->openAPINullablesSetToNull, 'cap');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cap', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($cap) && (mb_strlen($cap) > 8)) {
            throw new \InvalidArgumentException('invalid length for $cap when calling RisorsaFinanziaria., must be smaller than or equal to 8.');
        }
        if (!is_null($cap) && (mb_strlen($cap) < 0)) {
            throw new \InvalidArgumentException('invalid length for $cap when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['cap'] = $cap;

        return $this;
    }

    /**
     * Gets provincia
     *
     * @return string|null
     */
    public function getProvincia()
    {
        return $this->container['provincia'];
    }

    /**
     * Sets provincia
     *
     * @param string|null $provincia Provincia o denominazione dell'area.
     *
     * @return self
     */
    public function setProvincia($provincia)
    {
        if (is_null($provincia)) {
            array_push($this->openAPINullablesSetToNull, 'provincia');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provincia', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($provincia) && (mb_strlen($provincia) > 3)) {
            throw new \InvalidArgumentException('invalid length for $provincia when calling RisorsaFinanziaria., must be smaller than or equal to 3.');
        }
        if (!is_null($provincia) && (mb_strlen($provincia) < 0)) {
            throw new \InvalidArgumentException('invalid length for $provincia when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['provincia'] = $provincia;

        return $this;
    }

    /**
     * Gets telefono1
     *
     * @return string|null
     */
    public function getTelefono1()
    {
        return $this->container['telefono1'];
    }

    /**
     * Sets telefono1
     *
     * @param string|null $telefono1 Numero di telefono principale.
     *
     * @return self
     */
    public function setTelefono1($telefono1)
    {
        if (is_null($telefono1)) {
            array_push($this->openAPINullablesSetToNull, 'telefono1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('telefono1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($telefono1) && (mb_strlen($telefono1) > 25)) {
            throw new \InvalidArgumentException('invalid length for $telefono1 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($telefono1) && (mb_strlen($telefono1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $telefono1 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['telefono1'] = $telefono1;

        return $this;
    }

    /**
     * Gets telefono2
     *
     * @return string|null
     */
    public function getTelefono2()
    {
        return $this->container['telefono2'];
    }

    /**
     * Sets telefono2
     *
     * @param string|null $telefono2 Numero di telefono secondario.
     *
     * @return self
     */
    public function setTelefono2($telefono2)
    {
        if (is_null($telefono2)) {
            array_push($this->openAPINullablesSetToNull, 'telefono2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('telefono2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($telefono2) && (mb_strlen($telefono2) > 25)) {
            throw new \InvalidArgumentException('invalid length for $telefono2 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($telefono2) && (mb_strlen($telefono2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $telefono2 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['telefono2'] = $telefono2;

        return $this;
    }

    /**
     * Gets fax
     *
     * @return string|null
     */
    public function getFax()
    {
        return $this->container['fax'];
    }

    /**
     * Sets fax
     *
     * @param string|null $fax Numero di fax.
     *
     * @return self
     */
    public function setFax($fax)
    {
        if (is_null($fax)) {
            array_push($this->openAPINullablesSetToNull, 'fax');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('fax', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($fax) && (mb_strlen($fax) > 25)) {
            throw new \InvalidArgumentException('invalid length for $fax when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($fax) && (mb_strlen($fax) < 0)) {
            throw new \InvalidArgumentException('invalid length for $fax when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['fax'] = $fax;

        return $this;
    }

    /**
     * Gets contatto1_nome
     *
     * @return string|null
     */
    public function getContatto1Nome()
    {
        return $this->container['contatto1_nome'];
    }

    /**
     * Sets contatto1_nome
     *
     * @param string|null $contatto1_nome Nome del contatto.
     *
     * @return self
     */
    public function setContatto1Nome($contatto1_nome)
    {
        if (is_null($contatto1_nome)) {
            array_push($this->openAPINullablesSetToNull, 'contatto1_nome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto1_nome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto1_nome) && (mb_strlen($contatto1_nome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_nome when calling RisorsaFinanziaria., must be smaller than or equal to 60.');
        }
        if (!is_null($contatto1_nome) && (mb_strlen($contatto1_nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_nome when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto1_nome'] = $contatto1_nome;

        return $this;
    }

    /**
     * Gets contatto1_incarico
     *
     * @return string|null
     */
    public function getContatto1Incarico()
    {
        return $this->container['contatto1_incarico'];
    }

    /**
     * Sets contatto1_incarico
     *
     * @param string|null $contatto1_incarico Qualifica o ruolo del contatto.
     *
     * @return self
     */
    public function setContatto1Incarico($contatto1_incarico)
    {
        if (is_null($contatto1_incarico)) {
            array_push($this->openAPINullablesSetToNull, 'contatto1_incarico');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto1_incarico', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto1_incarico) && (mb_strlen($contatto1_incarico) > 40)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_incarico when calling RisorsaFinanziaria., must be smaller than or equal to 40.');
        }
        if (!is_null($contatto1_incarico) && (mb_strlen($contatto1_incarico) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_incarico when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto1_incarico'] = $contatto1_incarico;

        return $this;
    }

    /**
     * Gets contatto1_telefono1
     *
     * @return string|null
     */
    public function getContatto1Telefono1()
    {
        return $this->container['contatto1_telefono1'];
    }

    /**
     * Sets contatto1_telefono1
     *
     * @param string|null $contatto1_telefono1 Telefono principale del contatto.
     *
     * @return self
     */
    public function setContatto1Telefono1($contatto1_telefono1)
    {
        if (is_null($contatto1_telefono1)) {
            array_push($this->openAPINullablesSetToNull, 'contatto1_telefono1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto1_telefono1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto1_telefono1) && (mb_strlen($contatto1_telefono1) > 25)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_telefono1 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($contatto1_telefono1) && (mb_strlen($contatto1_telefono1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_telefono1 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto1_telefono1'] = $contatto1_telefono1;

        return $this;
    }

    /**
     * Gets contatto1_telefono2
     *
     * @return string|null
     */
    public function getContatto1Telefono2()
    {
        return $this->container['contatto1_telefono2'];
    }

    /**
     * Sets contatto1_telefono2
     *
     * @param string|null $contatto1_telefono2 Telefono secondario del contatto.
     *
     * @return self
     */
    public function setContatto1Telefono2($contatto1_telefono2)
    {
        if (is_null($contatto1_telefono2)) {
            array_push($this->openAPINullablesSetToNull, 'contatto1_telefono2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto1_telefono2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto1_telefono2) && (mb_strlen($contatto1_telefono2) > 25)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_telefono2 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($contatto1_telefono2) && (mb_strlen($contatto1_telefono2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_telefono2 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto1_telefono2'] = $contatto1_telefono2;

        return $this;
    }

    /**
     * Gets contatto1_e_mail
     *
     * @return string|null
     */
    public function getContatto1EMail()
    {
        return $this->container['contatto1_e_mail'];
    }

    /**
     * Sets contatto1_e_mail
     *
     * @param string|null $contatto1_e_mail Email del contatto.
     *
     * @return self
     */
    public function setContatto1EMail($contatto1_e_mail)
    {
        if (is_null($contatto1_e_mail)) {
            array_push($this->openAPINullablesSetToNull, 'contatto1_e_mail');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto1_e_mail', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto1_e_mail) && (mb_strlen($contatto1_e_mail) > 50)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_e_mail when calling RisorsaFinanziaria., must be smaller than or equal to 50.');
        }
        if (!is_null($contatto1_e_mail) && (mb_strlen($contatto1_e_mail) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto1_e_mail when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto1_e_mail'] = $contatto1_e_mail;

        return $this;
    }

    /**
     * Gets contatto2_nome
     *
     * @return string|null
     */
    public function getContatto2Nome()
    {
        return $this->container['contatto2_nome'];
    }

    /**
     * Sets contatto2_nome
     *
     * @param string|null $contatto2_nome Nome del contatto secondario.
     *
     * @return self
     */
    public function setContatto2Nome($contatto2_nome)
    {
        if (is_null($contatto2_nome)) {
            array_push($this->openAPINullablesSetToNull, 'contatto2_nome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto2_nome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto2_nome) && (mb_strlen($contatto2_nome) > 60)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_nome when calling RisorsaFinanziaria., must be smaller than or equal to 60.');
        }
        if (!is_null($contatto2_nome) && (mb_strlen($contatto2_nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_nome when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto2_nome'] = $contatto2_nome;

        return $this;
    }

    /**
     * Gets contatto2_incarico
     *
     * @return string|null
     */
    public function getContatto2Incarico()
    {
        return $this->container['contatto2_incarico'];
    }

    /**
     * Sets contatto2_incarico
     *
     * @param string|null $contatto2_incarico Qualifica o ruolo del contatto secondario.
     *
     * @return self
     */
    public function setContatto2Incarico($contatto2_incarico)
    {
        if (is_null($contatto2_incarico)) {
            array_push($this->openAPINullablesSetToNull, 'contatto2_incarico');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto2_incarico', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto2_incarico) && (mb_strlen($contatto2_incarico) > 40)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_incarico when calling RisorsaFinanziaria., must be smaller than or equal to 40.');
        }
        if (!is_null($contatto2_incarico) && (mb_strlen($contatto2_incarico) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_incarico when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto2_incarico'] = $contatto2_incarico;

        return $this;
    }

    /**
     * Gets contatto2_telefono1
     *
     * @return string|null
     */
    public function getContatto2Telefono1()
    {
        return $this->container['contatto2_telefono1'];
    }

    /**
     * Sets contatto2_telefono1
     *
     * @param string|null $contatto2_telefono1 Numero di telefono del contatto secondario.
     *
     * @return self
     */
    public function setContatto2Telefono1($contatto2_telefono1)
    {
        if (is_null($contatto2_telefono1)) {
            array_push($this->openAPINullablesSetToNull, 'contatto2_telefono1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto2_telefono1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto2_telefono1) && (mb_strlen($contatto2_telefono1) > 25)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_telefono1 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($contatto2_telefono1) && (mb_strlen($contatto2_telefono1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_telefono1 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto2_telefono1'] = $contatto2_telefono1;

        return $this;
    }

    /**
     * Gets contatto2_telefono2
     *
     * @return string|null
     */
    public function getContatto2Telefono2()
    {
        return $this->container['contatto2_telefono2'];
    }

    /**
     * Sets contatto2_telefono2
     *
     * @param string|null $contatto2_telefono2 Numero di telefono alternativo del contatto secondario.
     *
     * @return self
     */
    public function setContatto2Telefono2($contatto2_telefono2)
    {
        if (is_null($contatto2_telefono2)) {
            array_push($this->openAPINullablesSetToNull, 'contatto2_telefono2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto2_telefono2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto2_telefono2) && (mb_strlen($contatto2_telefono2) > 25)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_telefono2 when calling RisorsaFinanziaria., must be smaller than or equal to 25.');
        }
        if (!is_null($contatto2_telefono2) && (mb_strlen($contatto2_telefono2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_telefono2 when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto2_telefono2'] = $contatto2_telefono2;

        return $this;
    }

    /**
     * Gets contatto2_e_mail
     *
     * @return string|null
     */
    public function getContatto2EMail()
    {
        return $this->container['contatto2_e_mail'];
    }

    /**
     * Sets contatto2_e_mail
     *
     * @param string|null $contatto2_e_mail Email del contatto secondario.
     *
     * @return self
     */
    public function setContatto2EMail($contatto2_e_mail)
    {
        if (is_null($contatto2_e_mail)) {
            array_push($this->openAPINullablesSetToNull, 'contatto2_e_mail');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto2_e_mail', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($contatto2_e_mail) && (mb_strlen($contatto2_e_mail) > 50)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_e_mail when calling RisorsaFinanziaria., must be smaller than or equal to 50.');
        }
        if (!is_null($contatto2_e_mail) && (mb_strlen($contatto2_e_mail) < 0)) {
            throw new \InvalidArgumentException('invalid length for $contatto2_e_mail when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['contatto2_e_mail'] = $contatto2_e_mail;

        return $this;
    }

    /**
     * Gets codice_abi
     *
     * @return string|null
     */
    public function getCodiceAbi()
    {
        return $this->container['codice_abi'];
    }

    /**
     * Sets codice_abi
     *
     * @param string|null $codice_abi Codice ABI.
     *
     * @return self
     */
    public function setCodiceAbi($codice_abi)
    {
        if (is_null($codice_abi)) {
            array_push($this->openAPINullablesSetToNull, 'codice_abi');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_abi', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_abi) && (mb_strlen($codice_abi) > 10)) {
            throw new \InvalidArgumentException('invalid length for $codice_abi when calling RisorsaFinanziaria., must be smaller than or equal to 10.');
        }
        if (!is_null($codice_abi) && (mb_strlen($codice_abi) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_abi when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_abi'] = $codice_abi;

        return $this;
    }

    /**
     * Gets codice_cab
     *
     * @return string|null
     */
    public function getCodiceCab()
    {
        return $this->container['codice_cab'];
    }

    /**
     * Sets codice_cab
     *
     * @param string|null $codice_cab Codice CAB.
     *
     * @return self
     */
    public function setCodiceCab($codice_cab)
    {
        if (is_null($codice_cab)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cab');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cab', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cab) && (mb_strlen($codice_cab) > 10)) {
            throw new \InvalidArgumentException('invalid length for $codice_cab when calling RisorsaFinanziaria., must be smaller than or equal to 10.');
        }
        if (!is_null($codice_cab) && (mb_strlen($codice_cab) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cab when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_cab'] = $codice_cab;

        return $this;
    }

    /**
     * Gets conto_corrente
     *
     * @return string|null
     */
    public function getContoCorrente()
    {
        return $this->container['conto_corrente'];
    }

    /**
     * Sets conto_corrente
     *
     * @param string|null $conto_corrente Numero di conto corrente.
     *
     * @return self
     */
    public function setContoCorrente($conto_corrente)
    {
        if (is_null($conto_corrente)) {
            array_push($this->openAPINullablesSetToNull, 'conto_corrente');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('conto_corrente', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($conto_corrente) && (mb_strlen($conto_corrente) > 20)) {
            throw new \InvalidArgumentException('invalid length for $conto_corrente when calling RisorsaFinanziaria., must be smaller than or equal to 20.');
        }
        if (!is_null($conto_corrente) && (mb_strlen($conto_corrente) < 0)) {
            throw new \InvalidArgumentException('invalid length for $conto_corrente when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['conto_corrente'] = $conto_corrente;

        return $this;
    }

    /**
     * Gets codice_cin
     *
     * @return string|null
     */
    public function getCodiceCin()
    {
        return $this->container['codice_cin'];
    }

    /**
     * Sets codice_cin
     *
     * @param string|null $codice_cin Codice CIN.
     *
     * @return self
     */
    public function setCodiceCin($codice_cin)
    {
        if (is_null($codice_cin)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cin');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cin', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cin) && (mb_strlen($codice_cin) > 1)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin when calling RisorsaFinanziaria., must be smaller than or equal to 1.');
        }
        if (!is_null($codice_cin) && (mb_strlen($codice_cin) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_cin'] = $codice_cin;

        return $this;
    }

    /**
     * Gets codice_cin_eur
     *
     * @return string|null
     */
    public function getCodiceCinEur()
    {
        return $this->container['codice_cin_eur'];
    }

    /**
     * Sets codice_cin_eur
     *
     * @param string|null $codice_cin_eur CIN EUR.
     *
     * @return self
     */
    public function setCodiceCinEur($codice_cin_eur)
    {
        if (is_null($codice_cin_eur)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cin_eur');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cin_eur', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cin_eur) && (mb_strlen($codice_cin_eur) > 2)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin_eur when calling RisorsaFinanziaria., must be smaller than or equal to 2.');
        }
        if (!is_null($codice_cin_eur) && (mb_strlen($codice_cin_eur) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin_eur when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_cin_eur'] = $codice_cin_eur;

        return $this;
    }

    /**
     * Gets codice_cin_paese
     *
     * @return string|null
     */
    public function getCodiceCinPaese()
    {
        return $this->container['codice_cin_paese'];
    }

    /**
     * Sets codice_cin_paese
     *
     * @param string|null $codice_cin_paese Codice CIN della nazione.
     *
     * @return self
     */
    public function setCodiceCinPaese($codice_cin_paese)
    {
        if (is_null($codice_cin_paese)) {
            array_push($this->openAPINullablesSetToNull, 'codice_cin_paese');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_cin_paese', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_cin_paese) && (mb_strlen($codice_cin_paese) > 2)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin_paese when calling RisorsaFinanziaria., must be smaller than or equal to 2.');
        }
        if (!is_null($codice_cin_paese) && (mb_strlen($codice_cin_paese) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_cin_paese when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_cin_paese'] = $codice_cin_paese;

        return $this;
    }

    /**
     * Gets codice_swift
     *
     * @return string|null
     */
    public function getCodiceSwift()
    {
        return $this->container['codice_swift'];
    }

    /**
     * Sets codice_swift
     *
     * @param string|null $codice_swift Codice SWIFT.
     *
     * @return self
     */
    public function setCodiceSwift($codice_swift)
    {
        if (is_null($codice_swift)) {
            array_push($this->openAPINullablesSetToNull, 'codice_swift');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_swift', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_swift) && (mb_strlen($codice_swift) > 15)) {
            throw new \InvalidArgumentException('invalid length for $codice_swift when calling RisorsaFinanziaria., must be smaller than or equal to 15.');
        }
        if (!is_null($codice_swift) && (mb_strlen($codice_swift) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_swift when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_swift'] = $codice_swift;

        return $this;
    }

    /**
     * Gets codice_iban
     *
     * @return string|null
     */
    public function getCodiceIban()
    {
        return $this->container['codice_iban'];
    }

    /**
     * Sets codice_iban
     *
     * @param string|null $codice_iban Codice IBAN.
     *
     * @return self
     */
    public function setCodiceIban($codice_iban)
    {
        if (is_null($codice_iban)) {
            array_push($this->openAPINullablesSetToNull, 'codice_iban');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('codice_iban', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($codice_iban) && (mb_strlen($codice_iban) > 34)) {
            throw new \InvalidArgumentException('invalid length for $codice_iban when calling RisorsaFinanziaria., must be smaller than or equal to 34.');
        }
        if (!is_null($codice_iban) && (mb_strlen($codice_iban) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice_iban when calling RisorsaFinanziaria., must be bigger than or equal to 0.');
        }

        $this->container['codice_iban'] = $codice_iban;

        return $this;
    }

    /**
     * Gets web
     *
     * @return string|null
     */
    public function getWeb()
    {
        return $this->container['web'];
    }

    /**
     * Sets web
     *
     * @param string|null $web Sito web.
     *
     * @return self
     */
    public function setWeb($web)
    {
        if (is_null($web)) {
            array_push($this->openAPINullablesSetToNull, 'web');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('web', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['web'] = $web;

        return $this;
    }

    /**
     * Gets tipologia
     *
     * @return string|null
     */
    public function getTipologia()
    {
        return $this->container['tipologia'];
    }

    /**
     * Sets tipologia
     *
     * @param string|null $tipologia Tipologia di risorsa finanziaria.
     *
     * @return self
     */
    public function setTipologia($tipologia)
    {
        if (is_null($tipologia)) {
            throw new \InvalidArgumentException('non-nullable tipologia cannot be null');
        }
        $allowedValues = $this->getTipologiaAllowableValues();
        if (!in_array($tipologia, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipologia', must be one of '%s'",
                    $tipologia,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipologia'] = $tipologia;

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


