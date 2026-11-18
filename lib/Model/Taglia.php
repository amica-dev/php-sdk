<?php
/**
 * Taglia
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
 * Taglia Class Doc Comment
 *
 * @category Class
 * @description Taglia.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Taglia implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Taglia';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'nome' => 'string',
        'taglia1' => 'string',
        'taglia2' => 'string',
        'taglia3' => 'string',
        'taglia4' => 'string',
        'taglia5' => 'string',
        'taglia6' => 'string',
        'taglia7' => 'string',
        'taglia8' => 'string',
        'taglia9' => 'string',
        'taglia10' => 'string',
        'taglia11' => 'string',
        'taglia12' => 'string',
        'taglia13' => 'string',
        'taglia14' => 'string',
        'taglia15' => 'string',
        'taglia16' => 'string'
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
        'taglia1' => null,
        'taglia2' => null,
        'taglia3' => null,
        'taglia4' => null,
        'taglia5' => null,
        'taglia6' => null,
        'taglia7' => null,
        'taglia8' => null,
        'taglia9' => null,
        'taglia10' => null,
        'taglia11' => null,
        'taglia12' => null,
        'taglia13' => null,
        'taglia14' => null,
        'taglia15' => null,
        'taglia16' => null
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
        'taglia1' => true,
        'taglia2' => true,
        'taglia3' => true,
        'taglia4' => true,
        'taglia5' => true,
        'taglia6' => true,
        'taglia7' => true,
        'taglia8' => true,
        'taglia9' => true,
        'taglia10' => true,
        'taglia11' => true,
        'taglia12' => true,
        'taglia13' => true,
        'taglia14' => true,
        'taglia15' => true,
        'taglia16' => true
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
        'taglia1' => 'taglia1',
        'taglia2' => 'taglia2',
        'taglia3' => 'taglia3',
        'taglia4' => 'taglia4',
        'taglia5' => 'taglia5',
        'taglia6' => 'taglia6',
        'taglia7' => 'taglia7',
        'taglia8' => 'taglia8',
        'taglia9' => 'taglia9',
        'taglia10' => 'taglia10',
        'taglia11' => 'taglia11',
        'taglia12' => 'taglia12',
        'taglia13' => 'taglia13',
        'taglia14' => 'taglia14',
        'taglia15' => 'taglia15',
        'taglia16' => 'taglia16'
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
        'taglia1' => 'setTaglia1',
        'taglia2' => 'setTaglia2',
        'taglia3' => 'setTaglia3',
        'taglia4' => 'setTaglia4',
        'taglia5' => 'setTaglia5',
        'taglia6' => 'setTaglia6',
        'taglia7' => 'setTaglia7',
        'taglia8' => 'setTaglia8',
        'taglia9' => 'setTaglia9',
        'taglia10' => 'setTaglia10',
        'taglia11' => 'setTaglia11',
        'taglia12' => 'setTaglia12',
        'taglia13' => 'setTaglia13',
        'taglia14' => 'setTaglia14',
        'taglia15' => 'setTaglia15',
        'taglia16' => 'setTaglia16'
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
        'taglia1' => 'getTaglia1',
        'taglia2' => 'getTaglia2',
        'taglia3' => 'getTaglia3',
        'taglia4' => 'getTaglia4',
        'taglia5' => 'getTaglia5',
        'taglia6' => 'getTaglia6',
        'taglia7' => 'getTaglia7',
        'taglia8' => 'getTaglia8',
        'taglia9' => 'getTaglia9',
        'taglia10' => 'getTaglia10',
        'taglia11' => 'getTaglia11',
        'taglia12' => 'getTaglia12',
        'taglia13' => 'getTaglia13',
        'taglia14' => 'getTaglia14',
        'taglia15' => 'getTaglia15',
        'taglia16' => 'getTaglia16'
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
        $this->setIfExists('nome', $data ?? [], null);
        $this->setIfExists('taglia1', $data ?? [], null);
        $this->setIfExists('taglia2', $data ?? [], null);
        $this->setIfExists('taglia3', $data ?? [], null);
        $this->setIfExists('taglia4', $data ?? [], null);
        $this->setIfExists('taglia5', $data ?? [], null);
        $this->setIfExists('taglia6', $data ?? [], null);
        $this->setIfExists('taglia7', $data ?? [], null);
        $this->setIfExists('taglia8', $data ?? [], null);
        $this->setIfExists('taglia9', $data ?? [], null);
        $this->setIfExists('taglia10', $data ?? [], null);
        $this->setIfExists('taglia11', $data ?? [], null);
        $this->setIfExists('taglia12', $data ?? [], null);
        $this->setIfExists('taglia13', $data ?? [], null);
        $this->setIfExists('taglia14', $data ?? [], null);
        $this->setIfExists('taglia15', $data ?? [], null);
        $this->setIfExists('taglia16', $data ?? [], null);
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
        if ((mb_strlen($this->container['nome']) > 40)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be smaller than or equal to 40.";
        }

        if ((mb_strlen($this->container['nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia1']) && (mb_strlen($this->container['taglia1']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia1', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia1']) && (mb_strlen($this->container['taglia1']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia1', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia2']) && (mb_strlen($this->container['taglia2']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia2', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia2']) && (mb_strlen($this->container['taglia2']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia2', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia3']) && (mb_strlen($this->container['taglia3']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia3', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia3']) && (mb_strlen($this->container['taglia3']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia3', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia4']) && (mb_strlen($this->container['taglia4']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia4', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia4']) && (mb_strlen($this->container['taglia4']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia4', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia5']) && (mb_strlen($this->container['taglia5']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia5', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia5']) && (mb_strlen($this->container['taglia5']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia5', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia6']) && (mb_strlen($this->container['taglia6']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia6', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia6']) && (mb_strlen($this->container['taglia6']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia6', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia7']) && (mb_strlen($this->container['taglia7']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia7', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia7']) && (mb_strlen($this->container['taglia7']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia7', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia8']) && (mb_strlen($this->container['taglia8']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia8', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia8']) && (mb_strlen($this->container['taglia8']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia8', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia9']) && (mb_strlen($this->container['taglia9']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia9', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia9']) && (mb_strlen($this->container['taglia9']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia9', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia10']) && (mb_strlen($this->container['taglia10']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia10', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia10']) && (mb_strlen($this->container['taglia10']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia10', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia11']) && (mb_strlen($this->container['taglia11']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia11', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia11']) && (mb_strlen($this->container['taglia11']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia11', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia12']) && (mb_strlen($this->container['taglia12']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia12', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia12']) && (mb_strlen($this->container['taglia12']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia12', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia13']) && (mb_strlen($this->container['taglia13']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia13', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia13']) && (mb_strlen($this->container['taglia13']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia13', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia14']) && (mb_strlen($this->container['taglia14']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia14', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia14']) && (mb_strlen($this->container['taglia14']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia14', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia15']) && (mb_strlen($this->container['taglia15']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia15', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia15']) && (mb_strlen($this->container['taglia15']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia15', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['taglia16']) && (mb_strlen($this->container['taglia16']) > 5)) {
            $invalidProperties[] = "invalid value for 'taglia16', the character length must be smaller than or equal to 5.";
        }

        if (!is_null($this->container['taglia16']) && (mb_strlen($this->container['taglia16']) < 0)) {
            $invalidProperties[] = "invalid value for 'taglia16', the character length must be bigger than or equal to 0.";
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
     * @param string $nome Nome.
     *
     * @return self
     */
    public function setNome($nome)
    {
        if (is_null($nome)) {
            throw new \InvalidArgumentException('non-nullable nome cannot be null');
        }
        if ((mb_strlen($nome) > 40)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling Taglia., must be smaller than or equal to 40.');
        }
        if ((mb_strlen($nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['nome'] = $nome;

        return $this;
    }

    /**
     * Gets taglia1
     *
     * @return string|null
     */
    public function getTaglia1()
    {
        return $this->container['taglia1'];
    }

    /**
     * Sets taglia1
     *
     * @param string|null $taglia1 Identificativo taglia #1 (es. 'XL').
     *
     * @return self
     */
    public function setTaglia1($taglia1)
    {
        if (is_null($taglia1)) {
            array_push($this->openAPINullablesSetToNull, 'taglia1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia1) && (mb_strlen($taglia1) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia1 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia1) && (mb_strlen($taglia1) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia1 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia1'] = $taglia1;

        return $this;
    }

    /**
     * Gets taglia2
     *
     * @return string|null
     */
    public function getTaglia2()
    {
        return $this->container['taglia2'];
    }

    /**
     * Sets taglia2
     *
     * @param string|null $taglia2 Identificativo taglia #2.
     *
     * @return self
     */
    public function setTaglia2($taglia2)
    {
        if (is_null($taglia2)) {
            array_push($this->openAPINullablesSetToNull, 'taglia2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia2) && (mb_strlen($taglia2) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia2 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia2) && (mb_strlen($taglia2) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia2 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia2'] = $taglia2;

        return $this;
    }

    /**
     * Gets taglia3
     *
     * @return string|null
     */
    public function getTaglia3()
    {
        return $this->container['taglia3'];
    }

    /**
     * Sets taglia3
     *
     * @param string|null $taglia3 Identificativo taglia #3.
     *
     * @return self
     */
    public function setTaglia3($taglia3)
    {
        if (is_null($taglia3)) {
            array_push($this->openAPINullablesSetToNull, 'taglia3');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia3', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia3) && (mb_strlen($taglia3) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia3 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia3) && (mb_strlen($taglia3) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia3 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia3'] = $taglia3;

        return $this;
    }

    /**
     * Gets taglia4
     *
     * @return string|null
     */
    public function getTaglia4()
    {
        return $this->container['taglia4'];
    }

    /**
     * Sets taglia4
     *
     * @param string|null $taglia4 Identificativo taglia #4.
     *
     * @return self
     */
    public function setTaglia4($taglia4)
    {
        if (is_null($taglia4)) {
            array_push($this->openAPINullablesSetToNull, 'taglia4');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia4', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia4) && (mb_strlen($taglia4) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia4 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia4) && (mb_strlen($taglia4) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia4 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia4'] = $taglia4;

        return $this;
    }

    /**
     * Gets taglia5
     *
     * @return string|null
     */
    public function getTaglia5()
    {
        return $this->container['taglia5'];
    }

    /**
     * Sets taglia5
     *
     * @param string|null $taglia5 Identificativo taglia #5.
     *
     * @return self
     */
    public function setTaglia5($taglia5)
    {
        if (is_null($taglia5)) {
            array_push($this->openAPINullablesSetToNull, 'taglia5');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia5', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia5) && (mb_strlen($taglia5) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia5 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia5) && (mb_strlen($taglia5) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia5 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia5'] = $taglia5;

        return $this;
    }

    /**
     * Gets taglia6
     *
     * @return string|null
     */
    public function getTaglia6()
    {
        return $this->container['taglia6'];
    }

    /**
     * Sets taglia6
     *
     * @param string|null $taglia6 Identificativo taglia #6.
     *
     * @return self
     */
    public function setTaglia6($taglia6)
    {
        if (is_null($taglia6)) {
            array_push($this->openAPINullablesSetToNull, 'taglia6');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia6', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia6) && (mb_strlen($taglia6) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia6 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia6) && (mb_strlen($taglia6) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia6 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia6'] = $taglia6;

        return $this;
    }

    /**
     * Gets taglia7
     *
     * @return string|null
     */
    public function getTaglia7()
    {
        return $this->container['taglia7'];
    }

    /**
     * Sets taglia7
     *
     * @param string|null $taglia7 Identificativo taglia #7.
     *
     * @return self
     */
    public function setTaglia7($taglia7)
    {
        if (is_null($taglia7)) {
            array_push($this->openAPINullablesSetToNull, 'taglia7');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia7', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia7) && (mb_strlen($taglia7) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia7 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia7) && (mb_strlen($taglia7) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia7 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia7'] = $taglia7;

        return $this;
    }

    /**
     * Gets taglia8
     *
     * @return string|null
     */
    public function getTaglia8()
    {
        return $this->container['taglia8'];
    }

    /**
     * Sets taglia8
     *
     * @param string|null $taglia8 Identificativo taglia #8.
     *
     * @return self
     */
    public function setTaglia8($taglia8)
    {
        if (is_null($taglia8)) {
            array_push($this->openAPINullablesSetToNull, 'taglia8');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia8', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia8) && (mb_strlen($taglia8) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia8 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia8) && (mb_strlen($taglia8) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia8 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia8'] = $taglia8;

        return $this;
    }

    /**
     * Gets taglia9
     *
     * @return string|null
     */
    public function getTaglia9()
    {
        return $this->container['taglia9'];
    }

    /**
     * Sets taglia9
     *
     * @param string|null $taglia9 Identificativo taglia #9.
     *
     * @return self
     */
    public function setTaglia9($taglia9)
    {
        if (is_null($taglia9)) {
            array_push($this->openAPINullablesSetToNull, 'taglia9');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia9', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia9) && (mb_strlen($taglia9) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia9 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia9) && (mb_strlen($taglia9) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia9 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia9'] = $taglia9;

        return $this;
    }

    /**
     * Gets taglia10
     *
     * @return string|null
     */
    public function getTaglia10()
    {
        return $this->container['taglia10'];
    }

    /**
     * Sets taglia10
     *
     * @param string|null $taglia10 Identificativo taglia #10.
     *
     * @return self
     */
    public function setTaglia10($taglia10)
    {
        if (is_null($taglia10)) {
            array_push($this->openAPINullablesSetToNull, 'taglia10');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia10', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia10) && (mb_strlen($taglia10) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia10 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia10) && (mb_strlen($taglia10) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia10 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia10'] = $taglia10;

        return $this;
    }

    /**
     * Gets taglia11
     *
     * @return string|null
     */
    public function getTaglia11()
    {
        return $this->container['taglia11'];
    }

    /**
     * Sets taglia11
     *
     * @param string|null $taglia11 Identificativo taglia #11.
     *
     * @return self
     */
    public function setTaglia11($taglia11)
    {
        if (is_null($taglia11)) {
            array_push($this->openAPINullablesSetToNull, 'taglia11');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia11', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia11) && (mb_strlen($taglia11) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia11 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia11) && (mb_strlen($taglia11) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia11 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia11'] = $taglia11;

        return $this;
    }

    /**
     * Gets taglia12
     *
     * @return string|null
     */
    public function getTaglia12()
    {
        return $this->container['taglia12'];
    }

    /**
     * Sets taglia12
     *
     * @param string|null $taglia12 Identificativo taglia #12.
     *
     * @return self
     */
    public function setTaglia12($taglia12)
    {
        if (is_null($taglia12)) {
            array_push($this->openAPINullablesSetToNull, 'taglia12');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia12', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia12) && (mb_strlen($taglia12) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia12 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia12) && (mb_strlen($taglia12) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia12 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia12'] = $taglia12;

        return $this;
    }

    /**
     * Gets taglia13
     *
     * @return string|null
     */
    public function getTaglia13()
    {
        return $this->container['taglia13'];
    }

    /**
     * Sets taglia13
     *
     * @param string|null $taglia13 Identificativo taglia #13.
     *
     * @return self
     */
    public function setTaglia13($taglia13)
    {
        if (is_null($taglia13)) {
            array_push($this->openAPINullablesSetToNull, 'taglia13');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia13', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia13) && (mb_strlen($taglia13) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia13 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia13) && (mb_strlen($taglia13) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia13 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia13'] = $taglia13;

        return $this;
    }

    /**
     * Gets taglia14
     *
     * @return string|null
     */
    public function getTaglia14()
    {
        return $this->container['taglia14'];
    }

    /**
     * Sets taglia14
     *
     * @param string|null $taglia14 Identificativo taglia #14.
     *
     * @return self
     */
    public function setTaglia14($taglia14)
    {
        if (is_null($taglia14)) {
            array_push($this->openAPINullablesSetToNull, 'taglia14');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia14', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia14) && (mb_strlen($taglia14) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia14 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia14) && (mb_strlen($taglia14) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia14 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia14'] = $taglia14;

        return $this;
    }

    /**
     * Gets taglia15
     *
     * @return string|null
     */
    public function getTaglia15()
    {
        return $this->container['taglia15'];
    }

    /**
     * Sets taglia15
     *
     * @param string|null $taglia15 Identificativo taglia #15.
     *
     * @return self
     */
    public function setTaglia15($taglia15)
    {
        if (is_null($taglia15)) {
            array_push($this->openAPINullablesSetToNull, 'taglia15');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia15', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia15) && (mb_strlen($taglia15) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia15 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia15) && (mb_strlen($taglia15) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia15 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia15'] = $taglia15;

        return $this;
    }

    /**
     * Gets taglia16
     *
     * @return string|null
     */
    public function getTaglia16()
    {
        return $this->container['taglia16'];
    }

    /**
     * Sets taglia16
     *
     * @param string|null $taglia16 Identificativo taglia #16.
     *
     * @return self
     */
    public function setTaglia16($taglia16)
    {
        if (is_null($taglia16)) {
            array_push($this->openAPINullablesSetToNull, 'taglia16');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia16', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($taglia16) && (mb_strlen($taglia16) > 5)) {
            throw new \InvalidArgumentException('invalid length for $taglia16 when calling Taglia., must be smaller than or equal to 5.');
        }
        if (!is_null($taglia16) && (mb_strlen($taglia16) < 0)) {
            throw new \InvalidArgumentException('invalid length for $taglia16 when calling Taglia., must be bigger than or equal to 0.');
        }

        $this->container['taglia16'] = $taglia16;

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


