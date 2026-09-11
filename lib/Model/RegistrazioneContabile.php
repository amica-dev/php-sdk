<?php
/**
 * RegistrazioneContabile
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
 * The version of the OpenAPI document: 3.0.2
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
 * RegistrazioneContabile Class Doc Comment
 *
 * @category Class
 * @description Registrazione contabile.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class RegistrazioneContabile implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'RegistrazioneContabile';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'data' => '\DateTime',
        'data_competenza' => '\DateTime',
        'numero' => 'int',
        'tipo_registrazione' => 'string',
        'segno_iva' => 'string',
        'tipo_operazione' => 'string',
        'registro_iva_id' => 'int',
        'anagrafica_id' => 'int',
        'pagamento_id' => 'int',
        'documento_id' => 'int',
        'documento' => '\Amica\Sdk\Model\Documento',
        'data_documento' => '\DateTime',
        'documento_numero_parte_testo' => 'string',
        'documento_numero_parte_numerica' => 'int',
        'documento_numero' => 'string',
        'protocollo' => 'int',
        'descrizione' => 'string',
        'note' => 'string',
        'righe' => '\Amica\Sdk\Model\RegistrazioneContabileRiga[]',
        'castelletto_iva' => '\Amica\Sdk\Model\CastellettoIva[]',
        'scadenze' => '\Amica\Sdk\Model\Scadenza[]'
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
        'data' => 'date-time',
        'data_competenza' => 'date-time',
        'numero' => 'int32',
        'tipo_registrazione' => null,
        'segno_iva' => null,
        'tipo_operazione' => null,
        'registro_iva_id' => 'int32',
        'anagrafica_id' => 'int32',
        'pagamento_id' => 'int32',
        'documento_id' => 'int32',
        'documento' => null,
        'data_documento' => 'date-time',
        'documento_numero_parte_testo' => null,
        'documento_numero_parte_numerica' => 'int32',
        'documento_numero' => null,
        'protocollo' => 'int32',
        'descrizione' => null,
        'note' => null,
        'righe' => null,
        'castelletto_iva' => null,
        'scadenze' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'data' => false,
        'data_competenza' => true,
        'numero' => false,
        'tipo_registrazione' => false,
        'segno_iva' => false,
        'tipo_operazione' => false,
        'registro_iva_id' => true,
        'anagrafica_id' => true,
        'pagamento_id' => true,
        'documento_id' => true,
        'documento' => false,
        'data_documento' => true,
        'documento_numero_parte_testo' => true,
        'documento_numero_parte_numerica' => true,
        'documento_numero' => true,
        'protocollo' => true,
        'descrizione' => true,
        'note' => true,
        'righe' => true,
        'castelletto_iva' => true,
        'scadenze' => true
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
        'data' => 'data',
        'data_competenza' => 'dataCompetenza',
        'numero' => 'numero',
        'tipo_registrazione' => 'tipoRegistrazione',
        'segno_iva' => 'segnoIva',
        'tipo_operazione' => 'tipoOperazione',
        'registro_iva_id' => 'registroIvaId',
        'anagrafica_id' => 'anagraficaId',
        'pagamento_id' => 'pagamentoId',
        'documento_id' => 'documentoId',
        'documento' => 'documento',
        'data_documento' => 'dataDocumento',
        'documento_numero_parte_testo' => 'documentoNumeroParteTesto',
        'documento_numero_parte_numerica' => 'documentoNumeroParteNumerica',
        'documento_numero' => 'documentoNumero',
        'protocollo' => 'protocollo',
        'descrizione' => 'descrizione',
        'note' => 'note',
        'righe' => 'righe',
        'castelletto_iva' => 'castellettoIva',
        'scadenze' => 'scadenze'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'data' => 'setData',
        'data_competenza' => 'setDataCompetenza',
        'numero' => 'setNumero',
        'tipo_registrazione' => 'setTipoRegistrazione',
        'segno_iva' => 'setSegnoIva',
        'tipo_operazione' => 'setTipoOperazione',
        'registro_iva_id' => 'setRegistroIvaId',
        'anagrafica_id' => 'setAnagraficaId',
        'pagamento_id' => 'setPagamentoId',
        'documento_id' => 'setDocumentoId',
        'documento' => 'setDocumento',
        'data_documento' => 'setDataDocumento',
        'documento_numero_parte_testo' => 'setDocumentoNumeroParteTesto',
        'documento_numero_parte_numerica' => 'setDocumentoNumeroParteNumerica',
        'documento_numero' => 'setDocumentoNumero',
        'protocollo' => 'setProtocollo',
        'descrizione' => 'setDescrizione',
        'note' => 'setNote',
        'righe' => 'setRighe',
        'castelletto_iva' => 'setCastellettoIva',
        'scadenze' => 'setScadenze'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'data' => 'getData',
        'data_competenza' => 'getDataCompetenza',
        'numero' => 'getNumero',
        'tipo_registrazione' => 'getTipoRegistrazione',
        'segno_iva' => 'getSegnoIva',
        'tipo_operazione' => 'getTipoOperazione',
        'registro_iva_id' => 'getRegistroIvaId',
        'anagrafica_id' => 'getAnagraficaId',
        'pagamento_id' => 'getPagamentoId',
        'documento_id' => 'getDocumentoId',
        'documento' => 'getDocumento',
        'data_documento' => 'getDataDocumento',
        'documento_numero_parte_testo' => 'getDocumentoNumeroParteTesto',
        'documento_numero_parte_numerica' => 'getDocumentoNumeroParteNumerica',
        'documento_numero' => 'getDocumentoNumero',
        'protocollo' => 'getProtocollo',
        'descrizione' => 'getDescrizione',
        'note' => 'getNote',
        'righe' => 'getRighe',
        'castelletto_iva' => 'getCastellettoIva',
        'scadenze' => 'getScadenze'
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

    public const TIPO_REGISTRAZIONE_ACQUISTO = 'Acquisto';
    public const TIPO_REGISTRAZIONE_VENDITA = 'Vendita';
    public const TIPO_REGISTRAZIONE_CONTABILE = 'Contabile';
    public const SEGNO_IVA_POSITIVO = 'Positivo';
    public const SEGNO_IVA_NEGATIVO = 'Negativo';
    public const TIPO_OPERAZIONE_APERTURA = 'Apertura';
    public const TIPO_OPERAZIONE_CHIUSURA = 'Chiusura';
    public const TIPO_OPERAZIONE_NORMALE = 'Normale';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoRegistrazioneAllowableValues()
    {
        return [
            self::TIPO_REGISTRAZIONE_ACQUISTO,
            self::TIPO_REGISTRAZIONE_VENDITA,
            self::TIPO_REGISTRAZIONE_CONTABILE,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getSegnoIvaAllowableValues()
    {
        return [
            self::SEGNO_IVA_POSITIVO,
            self::SEGNO_IVA_NEGATIVO,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoOperazioneAllowableValues()
    {
        return [
            self::TIPO_OPERAZIONE_APERTURA,
            self::TIPO_OPERAZIONE_CHIUSURA,
            self::TIPO_OPERAZIONE_NORMALE,
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
        $this->setIfExists('data', $data ?? [], null);
        $this->setIfExists('data_competenza', $data ?? [], null);
        $this->setIfExists('numero', $data ?? [], null);
        $this->setIfExists('tipo_registrazione', $data ?? [], null);
        $this->setIfExists('segno_iva', $data ?? [], null);
        $this->setIfExists('tipo_operazione', $data ?? [], null);
        $this->setIfExists('registro_iva_id', $data ?? [], null);
        $this->setIfExists('anagrafica_id', $data ?? [], null);
        $this->setIfExists('pagamento_id', $data ?? [], null);
        $this->setIfExists('documento_id', $data ?? [], null);
        $this->setIfExists('documento', $data ?? [], null);
        $this->setIfExists('data_documento', $data ?? [], null);
        $this->setIfExists('documento_numero_parte_testo', $data ?? [], null);
        $this->setIfExists('documento_numero_parte_numerica', $data ?? [], null);
        $this->setIfExists('documento_numero', $data ?? [], null);
        $this->setIfExists('protocollo', $data ?? [], null);
        $this->setIfExists('descrizione', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
        $this->setIfExists('righe', $data ?? [], null);
        $this->setIfExists('castelletto_iva', $data ?? [], null);
        $this->setIfExists('scadenze', $data ?? [], null);
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

        if (!is_null($this->container['numero']) && ($this->container['numero'] > 2147483647)) {
            $invalidProperties[] = "invalid value for 'numero', must be smaller than or equal to 2147483647.";
        }

        if (!is_null($this->container['numero']) && ($this->container['numero'] < 1)) {
            $invalidProperties[] = "invalid value for 'numero', must be bigger than or equal to 1.";
        }

        $allowedValues = $this->getTipoRegistrazioneAllowableValues();
        if (!is_null($this->container['tipo_registrazione']) && !in_array($this->container['tipo_registrazione'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_registrazione', must be one of '%s'",
                $this->container['tipo_registrazione'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getSegnoIvaAllowableValues();
        if (!is_null($this->container['segno_iva']) && !in_array($this->container['segno_iva'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'segno_iva', must be one of '%s'",
                $this->container['segno_iva'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getTipoOperazioneAllowableValues();
        if (!is_null($this->container['tipo_operazione']) && !in_array($this->container['tipo_operazione'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_operazione', must be one of '%s'",
                $this->container['tipo_operazione'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['documento_numero_parte_testo']) && (mb_strlen($this->container['documento_numero_parte_testo']) > 40)) {
            $invalidProperties[] = "invalid value for 'documento_numero_parte_testo', the character length must be smaller than or equal to 40.";
        }

        if (!is_null($this->container['documento_numero_parte_testo']) && (mb_strlen($this->container['documento_numero_parte_testo']) < 0)) {
            $invalidProperties[] = "invalid value for 'documento_numero_parte_testo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['documento_numero_parte_numerica']) && ($this->container['documento_numero_parte_numerica'] > 2147483647)) {
            $invalidProperties[] = "invalid value for 'documento_numero_parte_numerica', must be smaller than or equal to 2147483647.";
        }

        if (!is_null($this->container['documento_numero_parte_numerica']) && ($this->container['documento_numero_parte_numerica'] < 1)) {
            $invalidProperties[] = "invalid value for 'documento_numero_parte_numerica', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['protocollo']) && ($this->container['protocollo'] > 2147483647)) {
            $invalidProperties[] = "invalid value for 'protocollo', must be smaller than or equal to 2147483647.";
        }

        if (!is_null($this->container['protocollo']) && ($this->container['protocollo'] < 1)) {
            $invalidProperties[] = "invalid value for 'protocollo', must be bigger than or equal to 1.";
        }

        if (!is_null($this->container['descrizione']) && (mb_strlen($this->container['descrizione']) > 60)) {
            $invalidProperties[] = "invalid value for 'descrizione', the character length must be smaller than or equal to 60.";
        }

        if (!is_null($this->container['descrizione']) && (mb_strlen($this->container['descrizione']) < 0)) {
            $invalidProperties[] = "invalid value for 'descrizione', the character length must be bigger than or equal to 0.";
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
     * Gets data
     *
     * @return \DateTime|null
     */
    public function getData()
    {
        return $this->container['data'];
    }

    /**
     * Sets data
     *
     * @param \DateTime|null $data Data della registrazione contabile.
     *
     * @return self
     */
    public function setData($data)
    {
        if (is_null($data)) {
            throw new \InvalidArgumentException('non-nullable data cannot be null');
        }
        $this->container['data'] = $data;

        return $this;
    }

    /**
     * Gets data_competenza
     *
     * @return \DateTime|null
     */
    public function getDataCompetenza()
    {
        return $this->container['data_competenza'];
    }

    /**
     * Sets data_competenza
     *
     * @param \DateTime|null $data_competenza Data di competenza.
     *
     * @return self
     */
    public function setDataCompetenza($data_competenza)
    {
        if (is_null($data_competenza)) {
            array_push($this->openAPINullablesSetToNull, 'data_competenza');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_competenza', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_competenza'] = $data_competenza;

        return $this;
    }

    /**
     * Gets numero
     *
     * @return int|null
     */
    public function getNumero()
    {
        return $this->container['numero'];
    }

    /**
     * Sets numero
     *
     * @param int|null $numero Numero della registrazione contabile.
     *
     * @return self
     */
    public function setNumero($numero)
    {
        if (is_null($numero)) {
            throw new \InvalidArgumentException('non-nullable numero cannot be null');
        }

        if (($numero > 2147483647)) {
            throw new \InvalidArgumentException('invalid value for $numero when calling RegistrazioneContabile., must be smaller than or equal to 2147483647.');
        }
        if (($numero < 1)) {
            throw new \InvalidArgumentException('invalid value for $numero when calling RegistrazioneContabile., must be bigger than or equal to 1.');
        }

        $this->container['numero'] = $numero;

        return $this;
    }

    /**
     * Gets tipo_registrazione
     *
     * @return string|null
     */
    public function getTipoRegistrazione()
    {
        return $this->container['tipo_registrazione'];
    }

    /**
     * Sets tipo_registrazione
     *
     * @param string|null $tipo_registrazione Tipo di registrazione.
     *
     * @return self
     */
    public function setTipoRegistrazione($tipo_registrazione)
    {
        if (is_null($tipo_registrazione)) {
            throw new \InvalidArgumentException('non-nullable tipo_registrazione cannot be null');
        }
        $allowedValues = $this->getTipoRegistrazioneAllowableValues();
        if (!in_array($tipo_registrazione, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_registrazione', must be one of '%s'",
                    $tipo_registrazione,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_registrazione'] = $tipo_registrazione;

        return $this;
    }

    /**
     * Gets segno_iva
     *
     * @return string|null
     */
    public function getSegnoIva()
    {
        return $this->container['segno_iva'];
    }

    /**
     * Sets segno_iva
     *
     * @param string|null $segno_iva Positività o negatività dell'IVA.
     *
     * @return self
     */
    public function setSegnoIva($segno_iva)
    {
        if (is_null($segno_iva)) {
            throw new \InvalidArgumentException('non-nullable segno_iva cannot be null');
        }
        $allowedValues = $this->getSegnoIvaAllowableValues();
        if (!in_array($segno_iva, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'segno_iva', must be one of '%s'",
                    $segno_iva,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['segno_iva'] = $segno_iva;

        return $this;
    }

    /**
     * Gets tipo_operazione
     *
     * @return string|null
     */
    public function getTipoOperazione()
    {
        return $this->container['tipo_operazione'];
    }

    /**
     * Sets tipo_operazione
     *
     * @param string|null $tipo_operazione Tipo di operazione contabile.
     *
     * @return self
     */
    public function setTipoOperazione($tipo_operazione)
    {
        if (is_null($tipo_operazione)) {
            throw new \InvalidArgumentException('non-nullable tipo_operazione cannot be null');
        }
        $allowedValues = $this->getTipoOperazioneAllowableValues();
        if (!in_array($tipo_operazione, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_operazione', must be one of '%s'",
                    $tipo_operazione,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_operazione'] = $tipo_operazione;

        return $this;
    }

    /**
     * Gets registro_iva_id
     *
     * @return int|null
     */
    public function getRegistroIvaId()
    {
        return $this->container['registro_iva_id'];
    }

    /**
     * Sets registro_iva_id
     *
     * @param int|null $registro_iva_id Identificativo univoco di `RegistroIva`.
     *
     * @return self
     */
    public function setRegistroIvaId($registro_iva_id)
    {
        if (is_null($registro_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'registro_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('registro_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['registro_iva_id'] = $registro_iva_id;

        return $this;
    }

    /**
     * Gets anagrafica_id
     *
     * @return int|null
     */
    public function getAnagraficaId()
    {
        return $this->container['anagrafica_id'];
    }

    /**
     * Sets anagrafica_id
     *
     * @param int|null $anagrafica_id Identificativo univoco di `Anagrafica`.
     *
     * @return self
     */
    public function setAnagraficaId($anagrafica_id)
    {
        if (is_null($anagrafica_id)) {
            array_push($this->openAPINullablesSetToNull, 'anagrafica_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('anagrafica_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['anagrafica_id'] = $anagrafica_id;

        return $this;
    }

    /**
     * Gets pagamento_id
     *
     * @return int|null
     */
    public function getPagamentoId()
    {
        return $this->container['pagamento_id'];
    }

    /**
     * Sets pagamento_id
     *
     * @param int|null $pagamento_id Identificativo univoco di `Pagamento`.
     *
     * @return self
     */
    public function setPagamentoId($pagamento_id)
    {
        if (is_null($pagamento_id)) {
            array_push($this->openAPINullablesSetToNull, 'pagamento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('pagamento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['pagamento_id'] = $pagamento_id;

        return $this;
    }

    /**
     * Gets documento_id
     *
     * @return int|null
     */
    public function getDocumentoId()
    {
        return $this->container['documento_id'];
    }

    /**
     * Sets documento_id
     *
     * @param int|null $documento_id Identificativo univoco di `Documento`.
     *
     * @return self
     */
    public function setDocumentoId($documento_id)
    {
        if (is_null($documento_id)) {
            array_push($this->openAPINullablesSetToNull, 'documento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documento_id'] = $documento_id;

        return $this;
    }

    /**
     * Gets documento
     *
     * @return \Amica\Sdk\Model\Documento|null
     */
    public function getDocumento()
    {
        return $this->container['documento'];
    }

    /**
     * Sets documento
     *
     * @param \Amica\Sdk\Model\Documento|null $documento documento
     *
     * @return self
     */
    public function setDocumento($documento)
    {
        if (is_null($documento)) {
            throw new \InvalidArgumentException('non-nullable documento cannot be null');
        }
        $this->container['documento'] = $documento;

        return $this;
    }

    /**
     * Gets data_documento
     *
     * @return \DateTime|null
     */
    public function getDataDocumento()
    {
        return $this->container['data_documento'];
    }

    /**
     * Sets data_documento
     *
     * @param \DateTime|null $data_documento Data del documento di riferimento.
     *
     * @return self
     */
    public function setDataDocumento($data_documento)
    {
        if (is_null($data_documento)) {
            array_push($this->openAPINullablesSetToNull, 'data_documento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_documento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_documento'] = $data_documento;

        return $this;
    }

    /**
     * Gets documento_numero_parte_testo
     *
     * @return string|null
     */
    public function getDocumentoNumeroParteTesto()
    {
        return $this->container['documento_numero_parte_testo'];
    }

    /**
     * Sets documento_numero_parte_testo
     *
     * @param string|null $documento_numero_parte_testo La parte alfanumerica del numero documento, se presente (es. \"bis\").
     *
     * @return self
     */
    public function setDocumentoNumeroParteTesto($documento_numero_parte_testo)
    {
        if (is_null($documento_numero_parte_testo)) {
            array_push($this->openAPINullablesSetToNull, 'documento_numero_parte_testo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_numero_parte_testo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($documento_numero_parte_testo) && (mb_strlen($documento_numero_parte_testo) > 40)) {
            throw new \InvalidArgumentException('invalid length for $documento_numero_parte_testo when calling RegistrazioneContabile., must be smaller than or equal to 40.');
        }
        if (!is_null($documento_numero_parte_testo) && (mb_strlen($documento_numero_parte_testo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $documento_numero_parte_testo when calling RegistrazioneContabile., must be bigger than or equal to 0.');
        }

        $this->container['documento_numero_parte_testo'] = $documento_numero_parte_testo;

        return $this;
    }

    /**
     * Gets documento_numero_parte_numerica
     *
     * @return int|null
     */
    public function getDocumentoNumeroParteNumerica()
    {
        return $this->container['documento_numero_parte_numerica'];
    }

    /**
     * Sets documento_numero_parte_numerica
     *
     * @param int|null $documento_numero_parte_numerica La parte numerica del numero documento (es. \"99\").
     *
     * @return self
     */
    public function setDocumentoNumeroParteNumerica($documento_numero_parte_numerica)
    {
        if (is_null($documento_numero_parte_numerica)) {
            array_push($this->openAPINullablesSetToNull, 'documento_numero_parte_numerica');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_numero_parte_numerica', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }

        if (!is_null($documento_numero_parte_numerica) && ($documento_numero_parte_numerica > 2147483647)) {
            throw new \InvalidArgumentException('invalid value for $documento_numero_parte_numerica when calling RegistrazioneContabile., must be smaller than or equal to 2147483647.');
        }
        if (!is_null($documento_numero_parte_numerica) && ($documento_numero_parte_numerica < 1)) {
            throw new \InvalidArgumentException('invalid value for $documento_numero_parte_numerica when calling RegistrazioneContabile., must be bigger than or equal to 1.');
        }

        $this->container['documento_numero_parte_numerica'] = $documento_numero_parte_numerica;

        return $this;
    }

    /**
     * Gets documento_numero
     *
     * @return string|null
     */
    public function getDocumentoNumero()
    {
        return $this->container['documento_numero'];
    }

    /**
     * Sets documento_numero
     *
     * @param string|null $documento_numero Numero documento completo, es. \"99/bis\". Il documento deve essere già stato salvato sul backend.
     *
     * @return self
     */
    public function setDocumentoNumero($documento_numero)
    {
        if (is_null($documento_numero)) {
            array_push($this->openAPINullablesSetToNull, 'documento_numero');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('documento_numero', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['documento_numero'] = $documento_numero;

        return $this;
    }

    /**
     * Gets protocollo
     *
     * @return int|null
     */
    public function getProtocollo()
    {
        return $this->container['protocollo'];
    }

    /**
     * Sets protocollo
     *
     * @param int|null $protocollo Numero di protocollo.
     *
     * @return self
     */
    public function setProtocollo($protocollo)
    {
        if (is_null($protocollo)) {
            array_push($this->openAPINullablesSetToNull, 'protocollo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('protocollo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }

        if (!is_null($protocollo) && ($protocollo > 2147483647)) {
            throw new \InvalidArgumentException('invalid value for $protocollo when calling RegistrazioneContabile., must be smaller than or equal to 2147483647.');
        }
        if (!is_null($protocollo) && ($protocollo < 1)) {
            throw new \InvalidArgumentException('invalid value for $protocollo when calling RegistrazioneContabile., must be bigger than or equal to 1.');
        }

        $this->container['protocollo'] = $protocollo;

        return $this;
    }

    /**
     * Gets descrizione
     *
     * @return string|null
     */
    public function getDescrizione()
    {
        return $this->container['descrizione'];
    }

    /**
     * Sets descrizione
     *
     * @param string|null $descrizione Descrizione.
     *
     * @return self
     */
    public function setDescrizione($descrizione)
    {
        if (is_null($descrizione)) {
            array_push($this->openAPINullablesSetToNull, 'descrizione');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('descrizione', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($descrizione) && (mb_strlen($descrizione) > 60)) {
            throw new \InvalidArgumentException('invalid length for $descrizione when calling RegistrazioneContabile., must be smaller than or equal to 60.');
        }
        if (!is_null($descrizione) && (mb_strlen($descrizione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $descrizione when calling RegistrazioneContabile., must be bigger than or equal to 0.');
        }

        $this->container['descrizione'] = $descrizione;

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
     * Gets righe
     *
     * @return \Amica\Sdk\Model\RegistrazioneContabileRiga[]|null
     */
    public function getRighe()
    {
        return $this->container['righe'];
    }

    /**
     * Sets righe
     *
     * @param \Amica\Sdk\Model\RegistrazioneContabileRiga[]|null $righe Righe della registrazione contabile.
     *
     * @return self
     */
    public function setRighe($righe)
    {
        if (is_null($righe)) {
            array_push($this->openAPINullablesSetToNull, 'righe');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('righe', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['righe'] = $righe;

        return $this;
    }

    /**
     * Gets castelletto_iva
     *
     * @return \Amica\Sdk\Model\CastellettoIva[]|null
     */
    public function getCastellettoIva()
    {
        return $this->container['castelletto_iva'];
    }

    /**
     * Sets castelletto_iva
     *
     * @param \Amica\Sdk\Model\CastellettoIva[]|null $castelletto_iva Importo IVA da versare.
     *
     * @return self
     */
    public function setCastellettoIva($castelletto_iva)
    {
        if (is_null($castelletto_iva)) {
            array_push($this->openAPINullablesSetToNull, 'castelletto_iva');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('castelletto_iva', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['castelletto_iva'] = $castelletto_iva;

        return $this;
    }

    /**
     * Gets scadenze
     *
     * @return \Amica\Sdk\Model\Scadenza[]|null
     */
    public function getScadenze()
    {
        return $this->container['scadenze'];
    }

    /**
     * Sets scadenze
     *
     * @param \Amica\Sdk\Model\Scadenza[]|null $scadenze Scadenze.
     *
     * @return self
     */
    public function setScadenze($scadenze)
    {
        if (is_null($scadenze)) {
            array_push($this->openAPINullablesSetToNull, 'scadenze');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('scadenze', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['scadenze'] = $scadenze;

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


