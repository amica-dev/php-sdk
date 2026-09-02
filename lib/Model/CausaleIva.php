<?php
/**
 * CausaleIva
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
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'[**App Store di Amica**](https://apps.amica20.it). Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  La scelta dipende da **come** la tua applicazione si autentica:  - C'è un **utente** che può accedere via browser (web app, SPA, app mobile)? → **Authorization Code con PKCE**. - È un **servizio headless** (backend, batch, integrazione server-to-server) di una **singola azienda**, senza un utente che effettua il login? → **Client Credentials**.  #### Authorization Code con PKCE  Consigliato per le app che operano per conto di un utente Amica ed è l'**unico flusso disponibile per le app pubblicate sul marketplace**. Una sola registrazione serve un numero qualsiasi di aziende: ogni utente autorizza la tua app per la propria azienda tramite la pagina di consenso dell'identity provider, e il token riceve automaticamente il contesto di quell'azienda. La tua app riceve un `code` da scambiare con un access token.  ``` GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256 POST {identity-provider}/connect/token      grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri> ```  #### Client Credentials  Per integrazioni **server-to-server** senza utente interattivo. Disponibile solo per le **app private**, non per quelle pubblicate sul marketplace. Un'app Client Credentials è **legata a una singola azienda** — quella dell'utente che la registra — e il token ne riceve automaticamente il contesto. Per servire un'azienda diversa serve una registrazione separata; se devi distribuire la stessa app a più aziende usa invece **Authorization Code**.  ``` POST {identity-provider}/connect/token Content-Type: application/x-www-form-urlencoded  grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=comune read write documenti ```  > **Importante:** non includere lo scope `openid` (né `profile`/`email`) in questo flusso. `openid` attiva OpenID Connect — richiede un utente che effettua il login e l'emissione di un ID token — cosa che un flusso server-to-server non prevede: la richiesta viene rifiutata con l'errore `Client cannot request OpenID scopes in client credentials flow`. Indica solo gli scope di risorsa (`comune`, `magazzino`, `documenti`, `contabilita`, ed eventualmente `read`/`write`).  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Il motivo dell'errore è sempre nel campo `detail`. Ogni risposta di errore riporta inoltre `overridable`: quando vale `true`, l'errore di validazione è *forzabile* e basta ripetere la stessa richiesta aggiungendo `validationOverride=true` alla query string perché il servizio la accetti. È il caso delle anomalie formali su dati reali — la partita IVA di un fornitore estero, per esempio — che il cliente ha comunque bisogno di registrare.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
 *
 * The version of the OpenAPI document: 3.0.1
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
 * CausaleIva Class Doc Comment
 *
 * @category Class
 * @description Causale IVA.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class CausaleIva implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'CausaleIva';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'codice' => 'string',
        'nome' => 'string',
        'aliquota' => 'float',
        'indeducibilit' => 'float',
        'intracomunitaria' => 'bool',
        'non_imponibile' => 'bool',
        'natura' => 'string',
        'split_payment' => 'bool',
        'fattura_elettronica' => 'bool'
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
        'codice' => null,
        'nome' => null,
        'aliquota' => 'double',
        'indeducibilit' => 'double',
        'intracomunitaria' => null,
        'non_imponibile' => null,
        'natura' => null,
        'split_payment' => null,
        'fattura_elettronica' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'codice' => false,
        'nome' => false,
        'aliquota' => false,
        'indeducibilit' => false,
        'intracomunitaria' => false,
        'non_imponibile' => false,
        'natura' => true,
        'split_payment' => false,
        'fattura_elettronica' => false
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
        'codice' => 'codice',
        'nome' => 'nome',
        'aliquota' => 'aliquota',
        'indeducibilit' => 'indeducibilità',
        'intracomunitaria' => 'intracomunitaria',
        'non_imponibile' => 'nonImponibile',
        'natura' => 'natura',
        'split_payment' => 'splitPayment',
        'fattura_elettronica' => 'fatturaElettronica'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'codice' => 'setCodice',
        'nome' => 'setNome',
        'aliquota' => 'setAliquota',
        'indeducibilit' => 'setIndeducibilit',
        'intracomunitaria' => 'setIntracomunitaria',
        'non_imponibile' => 'setNonImponibile',
        'natura' => 'setNatura',
        'split_payment' => 'setSplitPayment',
        'fattura_elettronica' => 'setFatturaElettronica'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'codice' => 'getCodice',
        'nome' => 'getNome',
        'aliquota' => 'getAliquota',
        'indeducibilit' => 'getIndeducibilit',
        'intracomunitaria' => 'getIntracomunitaria',
        'non_imponibile' => 'getNonImponibile',
        'natura' => 'getNatura',
        'split_payment' => 'getSplitPayment',
        'fattura_elettronica' => 'getFatturaElettronica'
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
        $this->setIfExists('codice', $data ?? [], null);
        $this->setIfExists('nome', $data ?? [], null);
        $this->setIfExists('aliquota', $data ?? [], null);
        $this->setIfExists('indeducibilit', $data ?? [], null);
        $this->setIfExists('intracomunitaria', $data ?? [], null);
        $this->setIfExists('non_imponibile', $data ?? [], null);
        $this->setIfExists('natura', $data ?? [], null);
        $this->setIfExists('split_payment', $data ?? [], null);
        $this->setIfExists('fattura_elettronica', $data ?? [], null);
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

        if ($this->container['codice'] === null) {
            $invalidProperties[] = "'codice' can't be null";
        }
        if ((mb_strlen($this->container['codice']) > 5)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be smaller than or equal to 5.";
        }

        if ((mb_strlen($this->container['codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be bigger than or equal to 0.";
        }

        if ($this->container['nome'] === null) {
            $invalidProperties[] = "'nome' can't be null";
        }
        if ((mb_strlen($this->container['nome']) > 50)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be smaller than or equal to 50.";
        }

        if ((mb_strlen($this->container['nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'nome', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['natura']) && (mb_strlen($this->container['natura']) > 4)) {
            $invalidProperties[] = "invalid value for 'natura', the character length must be smaller than or equal to 4.";
        }

        if (!is_null($this->container['natura']) && (mb_strlen($this->container['natura']) < 0)) {
            $invalidProperties[] = "invalid value for 'natura', the character length must be bigger than or equal to 0.";
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
     * Gets codice
     *
     * @return string
     */
    public function getCodice()
    {
        return $this->container['codice'];
    }

    /**
     * Sets codice
     *
     * @param string $codice Codice.
     *
     * @return self
     */
    public function setCodice($codice)
    {
        if (is_null($codice)) {
            throw new \InvalidArgumentException('non-nullable codice cannot be null');
        }
        if ((mb_strlen($codice) > 5)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling CausaleIva., must be smaller than or equal to 5.');
        }
        if ((mb_strlen($codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling CausaleIva., must be bigger than or equal to 0.');
        }

        $this->container['codice'] = $codice;

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
     * @param string $nome Nome, o descrizione.
     *
     * @return self
     */
    public function setNome($nome)
    {
        if (is_null($nome)) {
            throw new \InvalidArgumentException('non-nullable nome cannot be null');
        }
        if ((mb_strlen($nome) > 50)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling CausaleIva., must be smaller than or equal to 50.');
        }
        if ((mb_strlen($nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $nome when calling CausaleIva., must be bigger than or equal to 0.');
        }

        $this->container['nome'] = $nome;

        return $this;
    }

    /**
     * Gets aliquota
     *
     * @return float|null
     */
    public function getAliquota()
    {
        return $this->container['aliquota'];
    }

    /**
     * Sets aliquota
     *
     * @param float|null $aliquota Aliquota applicabile.
     *
     * @return self
     */
    public function setAliquota($aliquota)
    {
        if (is_null($aliquota)) {
            throw new \InvalidArgumentException('non-nullable aliquota cannot be null');
        }
        $this->container['aliquota'] = $aliquota;

        return $this;
    }

    /**
     * Gets indeducibilit
     *
     * @return float|null
     */
    public function getIndeducibilit()
    {
        return $this->container['indeducibilit'];
    }

    /**
     * Sets indeducibilit
     *
     * @param float|null $indeducibilit Quota di indeducibilità.
     *
     * @return self
     */
    public function setIndeducibilit($indeducibilit)
    {
        if (is_null($indeducibilit)) {
            throw new \InvalidArgumentException('non-nullable indeducibilit cannot be null');
        }
        $this->container['indeducibilit'] = $indeducibilit;

        return $this;
    }

    /**
     * Gets intracomunitaria
     *
     * @return bool|null
     */
    public function getIntracomunitaria()
    {
        return $this->container['intracomunitaria'];
    }

    /**
     * Sets intracomunitaria
     *
     * @param bool|null $intracomunitaria Indica se l'IVA è intracomunitaria.
     *
     * @return self
     */
    public function setIntracomunitaria($intracomunitaria)
    {
        if (is_null($intracomunitaria)) {
            throw new \InvalidArgumentException('non-nullable intracomunitaria cannot be null');
        }
        $this->container['intracomunitaria'] = $intracomunitaria;

        return $this;
    }

    /**
     * Gets non_imponibile
     *
     * @return bool|null
     */
    public function getNonImponibile()
    {
        return $this->container['non_imponibile'];
    }

    /**
     * Sets non_imponibile
     *
     * @param bool|null $non_imponibile Indica se l'IVA non deve essere considerata nella determinazione dell'imponibile.
     *
     * @return self
     */
    public function setNonImponibile($non_imponibile)
    {
        if (is_null($non_imponibile)) {
            throw new \InvalidArgumentException('non-nullable non_imponibile cannot be null');
        }
        $this->container['non_imponibile'] = $non_imponibile;

        return $this;
    }

    /**
     * Gets natura
     *
     * @return string|null
     */
    public function getNatura()
    {
        return $this->container['natura'];
    }

    /**
     * Sets natura
     *
     * @param string|null $natura Natura dell'esenzione (codice), se applicabile (ad es. \"N1\").
     *
     * @return self
     */
    public function setNatura($natura)
    {
        if (is_null($natura)) {
            array_push($this->openAPINullablesSetToNull, 'natura');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('natura', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($natura) && (mb_strlen($natura) > 4)) {
            throw new \InvalidArgumentException('invalid length for $natura when calling CausaleIva., must be smaller than or equal to 4.');
        }
        if (!is_null($natura) && (mb_strlen($natura) < 0)) {
            throw new \InvalidArgumentException('invalid length for $natura when calling CausaleIva., must be bigger than or equal to 0.');
        }

        $this->container['natura'] = $natura;

        return $this;
    }

    /**
     * Gets split_payment
     *
     * @return bool|null
     */
    public function getSplitPayment()
    {
        return $this->container['split_payment'];
    }

    /**
     * Sets split_payment
     *
     * @param bool|null $split_payment Indica se si applica lo split payment o meno.
     *
     * @return self
     */
    public function setSplitPayment($split_payment)
    {
        if (is_null($split_payment)) {
            throw new \InvalidArgumentException('non-nullable split_payment cannot be null');
        }
        $this->container['split_payment'] = $split_payment;

        return $this;
    }

    /**
     * Gets fattura_elettronica
     *
     * @return bool|null
     */
    public function getFatturaElettronica()
    {
        return $this->container['fattura_elettronica'];
    }

    /**
     * Sets fattura_elettronica
     *
     * @param bool|null $fattura_elettronica Indica se l'IVA è stata creata automaticamente durante l'importazione di una fattura elettronica.
     *
     * @return self
     */
    public function setFatturaElettronica($fattura_elettronica)
    {
        if (is_null($fattura_elettronica)) {
            throw new \InvalidArgumentException('non-nullable fattura_elettronica cannot be null');
        }
        $this->container['fattura_elettronica'] = $fattura_elettronica;

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


