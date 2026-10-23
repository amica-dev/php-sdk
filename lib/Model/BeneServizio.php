<?php
/**
 * BeneServizio
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
 * The version of the OpenAPI document: 3.3.0
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
 * BeneServizio Class Doc Comment
 *
 * @category Class
 * @description Bene o servizio fatturato.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class BeneServizio implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'BeneServizio';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'documento_id' => 'int',
        'documento' => '\Amica\Sdk\Model\Documento',
        'articolo_id' => 'int',
        'articolo' => '\Amica\Sdk\Model\Articolo',
        'prestazione_id' => 'int',
        'prestazione' => '\Amica\Sdk\Model\Prestazione',
        'magazzino_id' => 'int',
        'magazzino' => '\Amica\Sdk\Model\Magazzino',
        'causale_iva_id' => 'int',
        'causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'listino_id' => 'int',
        'listino' => '\Amica\Sdk\Model\Listino',
        'ordine_id' => 'int',
        'bene_servizio_ordine_id' => 'int',
        'taglia_id' => 'int',
        'taglia' => '\Amica\Sdk\Model\Taglia',
        'ritenuta_fiscale' => 'bool',
        'posizione' => 'int',
        'codice' => 'string',
        'descrizione' => 'string',
        'unit_misura' => 'string',
        'quantit' => 'float',
        'quantit_evasa' => 'float',
        'quantit_da_evadere' => 'float',
        'sconto1' => 'float',
        'sconto2' => 'float',
        'sconto3' => 'float',
        'sconto4' => 'float',
        'sconto_incondizionato' => 'float',
        'provvigione_agente' => 'float',
        'provvigione_capo_area' => 'float',
        'prezzo_id' => 'int',
        'prezzo' => 'float',
        'prezzo_scontato' => 'float',
        'prezzo_ivato' => 'float',
        'importo' => 'float',
        'importo_ivato' => 'float',
        'dati_aggiuntivi' => '\Amica\Sdk\Model\DatiAggiuntivi',
        'sconto' => 'string'
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
        'documento_id' => 'int32',
        'documento' => null,
        'articolo_id' => 'int32',
        'articolo' => null,
        'prestazione_id' => 'int32',
        'prestazione' => null,
        'magazzino_id' => 'int32',
        'magazzino' => null,
        'causale_iva_id' => 'int32',
        'causale_iva' => null,
        'listino_id' => 'int32',
        'listino' => null,
        'ordine_id' => 'int32',
        'bene_servizio_ordine_id' => 'int32',
        'taglia_id' => 'int32',
        'taglia' => null,
        'ritenuta_fiscale' => null,
        'posizione' => 'int32',
        'codice' => null,
        'descrizione' => null,
        'unit_misura' => null,
        'quantit' => 'double',
        'quantit_evasa' => 'double',
        'quantit_da_evadere' => 'double',
        'sconto1' => 'double',
        'sconto2' => 'double',
        'sconto3' => 'double',
        'sconto4' => 'double',
        'sconto_incondizionato' => 'double',
        'provvigione_agente' => 'double',
        'provvigione_capo_area' => 'double',
        'prezzo_id' => 'int32',
        'prezzo' => 'double',
        'prezzo_scontato' => 'double',
        'prezzo_ivato' => 'double',
        'importo' => 'double',
        'importo_ivato' => 'double',
        'dati_aggiuntivi' => null,
        'sconto' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'documento_id' => false,
        'documento' => false,
        'articolo_id' => true,
        'articolo' => false,
        'prestazione_id' => true,
        'prestazione' => false,
        'magazzino_id' => true,
        'magazzino' => false,
        'causale_iva_id' => true,
        'causale_iva' => false,
        'listino_id' => true,
        'listino' => false,
        'ordine_id' => true,
        'bene_servizio_ordine_id' => true,
        'taglia_id' => true,
        'taglia' => false,
        'ritenuta_fiscale' => false,
        'posizione' => false,
        'codice' => true,
        'descrizione' => true,
        'unit_misura' => true,
        'quantit' => true,
        'quantit_evasa' => false,
        'quantit_da_evadere' => false,
        'sconto1' => true,
        'sconto2' => true,
        'sconto3' => true,
        'sconto4' => true,
        'sconto_incondizionato' => true,
        'provvigione_agente' => true,
        'provvigione_capo_area' => true,
        'prezzo_id' => true,
        'prezzo' => true,
        'prezzo_scontato' => true,
        'prezzo_ivato' => true,
        'importo' => true,
        'importo_ivato' => true,
        'dati_aggiuntivi' => false,
        'sconto' => true
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
        'documento_id' => 'documentoId',
        'documento' => 'documento',
        'articolo_id' => 'articoloId',
        'articolo' => 'articolo',
        'prestazione_id' => 'prestazioneId',
        'prestazione' => 'prestazione',
        'magazzino_id' => 'magazzinoId',
        'magazzino' => 'magazzino',
        'causale_iva_id' => 'causaleIvaId',
        'causale_iva' => 'causaleIva',
        'listino_id' => 'listinoId',
        'listino' => 'listino',
        'ordine_id' => 'ordineId',
        'bene_servizio_ordine_id' => 'beneServizioOrdineId',
        'taglia_id' => 'tagliaId',
        'taglia' => 'taglia',
        'ritenuta_fiscale' => 'ritenutaFiscale',
        'posizione' => 'posizione',
        'codice' => 'codice',
        'descrizione' => 'descrizione',
        'unit_misura' => 'unitàMisura',
        'quantit' => 'quantità',
        'quantit_evasa' => 'quantitàEvasa',
        'quantit_da_evadere' => 'quantitàDaEvadere',
        'sconto1' => 'sconto1',
        'sconto2' => 'sconto2',
        'sconto3' => 'sconto3',
        'sconto4' => 'sconto4',
        'sconto_incondizionato' => 'scontoIncondizionato',
        'provvigione_agente' => 'provvigioneAgente',
        'provvigione_capo_area' => 'provvigioneCapoArea',
        'prezzo_id' => 'prezzoId',
        'prezzo' => 'prezzo',
        'prezzo_scontato' => 'prezzoScontato',
        'prezzo_ivato' => 'prezzoIvato',
        'importo' => 'importo',
        'importo_ivato' => 'importoIvato',
        'dati_aggiuntivi' => 'datiAggiuntivi',
        'sconto' => 'sconto'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'documento_id' => 'setDocumentoId',
        'documento' => 'setDocumento',
        'articolo_id' => 'setArticoloId',
        'articolo' => 'setArticolo',
        'prestazione_id' => 'setPrestazioneId',
        'prestazione' => 'setPrestazione',
        'magazzino_id' => 'setMagazzinoId',
        'magazzino' => 'setMagazzino',
        'causale_iva_id' => 'setCausaleIvaId',
        'causale_iva' => 'setCausaleIva',
        'listino_id' => 'setListinoId',
        'listino' => 'setListino',
        'ordine_id' => 'setOrdineId',
        'bene_servizio_ordine_id' => 'setBeneServizioOrdineId',
        'taglia_id' => 'setTagliaId',
        'taglia' => 'setTaglia',
        'ritenuta_fiscale' => 'setRitenutaFiscale',
        'posizione' => 'setPosizione',
        'codice' => 'setCodice',
        'descrizione' => 'setDescrizione',
        'unit_misura' => 'setUnitMisura',
        'quantit' => 'setQuantit',
        'quantit_evasa' => 'setQuantitEvasa',
        'quantit_da_evadere' => 'setQuantitDaEvadere',
        'sconto1' => 'setSconto1',
        'sconto2' => 'setSconto2',
        'sconto3' => 'setSconto3',
        'sconto4' => 'setSconto4',
        'sconto_incondizionato' => 'setScontoIncondizionato',
        'provvigione_agente' => 'setProvvigioneAgente',
        'provvigione_capo_area' => 'setProvvigioneCapoArea',
        'prezzo_id' => 'setPrezzoId',
        'prezzo' => 'setPrezzo',
        'prezzo_scontato' => 'setPrezzoScontato',
        'prezzo_ivato' => 'setPrezzoIvato',
        'importo' => 'setImporto',
        'importo_ivato' => 'setImportoIvato',
        'dati_aggiuntivi' => 'setDatiAggiuntivi',
        'sconto' => 'setSconto'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'documento_id' => 'getDocumentoId',
        'documento' => 'getDocumento',
        'articolo_id' => 'getArticoloId',
        'articolo' => 'getArticolo',
        'prestazione_id' => 'getPrestazioneId',
        'prestazione' => 'getPrestazione',
        'magazzino_id' => 'getMagazzinoId',
        'magazzino' => 'getMagazzino',
        'causale_iva_id' => 'getCausaleIvaId',
        'causale_iva' => 'getCausaleIva',
        'listino_id' => 'getListinoId',
        'listino' => 'getListino',
        'ordine_id' => 'getOrdineId',
        'bene_servizio_ordine_id' => 'getBeneServizioOrdineId',
        'taglia_id' => 'getTagliaId',
        'taglia' => 'getTaglia',
        'ritenuta_fiscale' => 'getRitenutaFiscale',
        'posizione' => 'getPosizione',
        'codice' => 'getCodice',
        'descrizione' => 'getDescrizione',
        'unit_misura' => 'getUnitMisura',
        'quantit' => 'getQuantit',
        'quantit_evasa' => 'getQuantitEvasa',
        'quantit_da_evadere' => 'getQuantitDaEvadere',
        'sconto1' => 'getSconto1',
        'sconto2' => 'getSconto2',
        'sconto3' => 'getSconto3',
        'sconto4' => 'getSconto4',
        'sconto_incondizionato' => 'getScontoIncondizionato',
        'provvigione_agente' => 'getProvvigioneAgente',
        'provvigione_capo_area' => 'getProvvigioneCapoArea',
        'prezzo_id' => 'getPrezzoId',
        'prezzo' => 'getPrezzo',
        'prezzo_scontato' => 'getPrezzoScontato',
        'prezzo_ivato' => 'getPrezzoIvato',
        'importo' => 'getImporto',
        'importo_ivato' => 'getImportoIvato',
        'dati_aggiuntivi' => 'getDatiAggiuntivi',
        'sconto' => 'getSconto'
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
        $this->setIfExists('documento_id', $data ?? [], null);
        $this->setIfExists('documento', $data ?? [], null);
        $this->setIfExists('articolo_id', $data ?? [], null);
        $this->setIfExists('articolo', $data ?? [], null);
        $this->setIfExists('prestazione_id', $data ?? [], null);
        $this->setIfExists('prestazione', $data ?? [], null);
        $this->setIfExists('magazzino_id', $data ?? [], null);
        $this->setIfExists('magazzino', $data ?? [], null);
        $this->setIfExists('causale_iva_id', $data ?? [], null);
        $this->setIfExists('causale_iva', $data ?? [], null);
        $this->setIfExists('listino_id', $data ?? [], null);
        $this->setIfExists('listino', $data ?? [], null);
        $this->setIfExists('ordine_id', $data ?? [], null);
        $this->setIfExists('bene_servizio_ordine_id', $data ?? [], null);
        $this->setIfExists('taglia_id', $data ?? [], null);
        $this->setIfExists('taglia', $data ?? [], null);
        $this->setIfExists('ritenuta_fiscale', $data ?? [], null);
        $this->setIfExists('posizione', $data ?? [], null);
        $this->setIfExists('codice', $data ?? [], null);
        $this->setIfExists('descrizione', $data ?? [], null);
        $this->setIfExists('unit_misura', $data ?? [], null);
        $this->setIfExists('quantit', $data ?? [], null);
        $this->setIfExists('quantit_evasa', $data ?? [], null);
        $this->setIfExists('quantit_da_evadere', $data ?? [], null);
        $this->setIfExists('sconto1', $data ?? [], null);
        $this->setIfExists('sconto2', $data ?? [], null);
        $this->setIfExists('sconto3', $data ?? [], null);
        $this->setIfExists('sconto4', $data ?? [], null);
        $this->setIfExists('sconto_incondizionato', $data ?? [], null);
        $this->setIfExists('provvigione_agente', $data ?? [], null);
        $this->setIfExists('provvigione_capo_area', $data ?? [], null);
        $this->setIfExists('prezzo_id', $data ?? [], null);
        $this->setIfExists('prezzo', $data ?? [], null);
        $this->setIfExists('prezzo_scontato', $data ?? [], null);
        $this->setIfExists('prezzo_ivato', $data ?? [], null);
        $this->setIfExists('importo', $data ?? [], null);
        $this->setIfExists('importo_ivato', $data ?? [], null);
        $this->setIfExists('dati_aggiuntivi', $data ?? [], null);
        $this->setIfExists('sconto', $data ?? [], null);
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

        if (!is_null($this->container['codice']) && (mb_strlen($this->container['codice']) > 30)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be smaller than or equal to 30.";
        }

        if (!is_null($this->container['codice']) && (mb_strlen($this->container['codice']) < 0)) {
            $invalidProperties[] = "invalid value for 'codice', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['unit_misura']) && (mb_strlen($this->container['unit_misura']) > 15)) {
            $invalidProperties[] = "invalid value for 'unit_misura', the character length must be smaller than or equal to 15.";
        }

        if (!is_null($this->container['unit_misura']) && (mb_strlen($this->container['unit_misura']) < 0)) {
            $invalidProperties[] = "invalid value for 'unit_misura', the character length must be bigger than or equal to 0.";
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
     * @param int|null $documento_id Identificativo univoco del `Documento` (documento).
     *
     * @return self
     */
    public function setDocumentoId($documento_id)
    {
        if (is_null($documento_id)) {
            throw new \InvalidArgumentException('non-nullable documento_id cannot be null');
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
     * Gets articolo_id
     *
     * @return int|null
     */
    public function getArticoloId()
    {
        return $this->container['articolo_id'];
    }

    /**
     * Sets articolo_id
     *
     * @param int|null $articolo_id Identificativo univoco dell'`Articolo` (articolo o prodotto).
     *
     * @return self
     */
    public function setArticoloId($articolo_id)
    {
        if (is_null($articolo_id)) {
            array_push($this->openAPINullablesSetToNull, 'articolo_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('articolo_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['articolo_id'] = $articolo_id;

        return $this;
    }

    /**
     * Gets articolo
     *
     * @return \Amica\Sdk\Model\Articolo|null
     */
    public function getArticolo()
    {
        return $this->container['articolo'];
    }

    /**
     * Sets articolo
     *
     * @param \Amica\Sdk\Model\Articolo|null $articolo articolo
     *
     * @return self
     */
    public function setArticolo($articolo)
    {
        if (is_null($articolo)) {
            throw new \InvalidArgumentException('non-nullable articolo cannot be null');
        }
        $this->container['articolo'] = $articolo;

        return $this;
    }

    /**
     * Gets prestazione_id
     *
     * @return int|null
     */
    public function getPrestazioneId()
    {
        return $this->container['prestazione_id'];
    }

    /**
     * Sets prestazione_id
     *
     * @param int|null $prestazione_id Identificativo univoco della `Prestazione` (prestazione).
     *
     * @return self
     */
    public function setPrestazioneId($prestazione_id)
    {
        if (is_null($prestazione_id)) {
            array_push($this->openAPINullablesSetToNull, 'prestazione_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prestazione_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prestazione_id'] = $prestazione_id;

        return $this;
    }

    /**
     * Gets prestazione
     *
     * @return \Amica\Sdk\Model\Prestazione|null
     */
    public function getPrestazione()
    {
        return $this->container['prestazione'];
    }

    /**
     * Sets prestazione
     *
     * @param \Amica\Sdk\Model\Prestazione|null $prestazione prestazione
     *
     * @return self
     */
    public function setPrestazione($prestazione)
    {
        if (is_null($prestazione)) {
            throw new \InvalidArgumentException('non-nullable prestazione cannot be null');
        }
        $this->container['prestazione'] = $prestazione;

        return $this;
    }

    /**
     * Gets magazzino_id
     *
     * @return int|null
     */
    public function getMagazzinoId()
    {
        return $this->container['magazzino_id'];
    }

    /**
     * Sets magazzino_id
     *
     * @param int|null $magazzino_id Identificativo univoco del `Magazzino` (magazzino). Obbligatorio quando viene usato `Codice`.
     *
     * @return self
     */
    public function setMagazzinoId($magazzino_id)
    {
        if (is_null($magazzino_id)) {
            array_push($this->openAPINullablesSetToNull, 'magazzino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('magazzino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['magazzino_id'] = $magazzino_id;

        return $this;
    }

    /**
     * Gets magazzino
     *
     * @return \Amica\Sdk\Model\Magazzino|null
     */
    public function getMagazzino()
    {
        return $this->container['magazzino'];
    }

    /**
     * Sets magazzino
     *
     * @param \Amica\Sdk\Model\Magazzino|null $magazzino magazzino
     *
     * @return self
     */
    public function setMagazzino($magazzino)
    {
        if (is_null($magazzino)) {
            throw new \InvalidArgumentException('non-nullable magazzino cannot be null');
        }
        $this->container['magazzino'] = $magazzino;

        return $this;
    }

    /**
     * Gets causale_iva_id
     *
     * @return int|null
     */
    public function getCausaleIvaId()
    {
        return $this->container['causale_iva_id'];
    }

    /**
     * Sets causale_iva_id
     *
     * @param int|null $causale_iva_id Identificativo univoco della `CausaleIva`.
     *
     * @return self
     */
    public function setCausaleIvaId($causale_iva_id)
    {
        if (is_null($causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_iva_id'] = $causale_iva_id;

        return $this;
    }

    /**
     * Gets causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getCausaleIva()
    {
        return $this->container['causale_iva'];
    }

    /**
     * Sets causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $causale_iva causale_iva
     *
     * @return self
     */
    public function setCausaleIva($causale_iva)
    {
        if (is_null($causale_iva)) {
            throw new \InvalidArgumentException('non-nullable causale_iva cannot be null');
        }
        $this->container['causale_iva'] = $causale_iva;

        return $this;
    }

    /**
     * Gets listino_id
     *
     * @return int|null
     */
    public function getListinoId()
    {
        return $this->container['listino_id'];
    }

    /**
     * Sets listino_id
     *
     * @param int|null $listino_id Identificativo univoco del `Listino` (listino).
     *
     * @return self
     */
    public function setListinoId($listino_id)
    {
        if (is_null($listino_id)) {
            array_push($this->openAPINullablesSetToNull, 'listino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('listino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['listino_id'] = $listino_id;

        return $this;
    }

    /**
     * Gets listino
     *
     * @return \Amica\Sdk\Model\Listino|null
     */
    public function getListino()
    {
        return $this->container['listino'];
    }

    /**
     * Sets listino
     *
     * @param \Amica\Sdk\Model\Listino|null $listino listino
     *
     * @return self
     */
    public function setListino($listino)
    {
        if (is_null($listino)) {
            throw new \InvalidArgumentException('non-nullable listino cannot be null');
        }
        $this->container['listino'] = $listino;

        return $this;
    }

    /**
     * Gets ordine_id
     *
     * @return int|null
     */
    public function getOrdineId()
    {
        return $this->container['ordine_id'];
    }

    /**
     * Sets ordine_id
     *
     * @param int|null $ordine_id Identificativo univoco del `Documento` (ordine).
     *
     * @return self
     */
    public function setOrdineId($ordine_id)
    {
        if (is_null($ordine_id)) {
            array_push($this->openAPINullablesSetToNull, 'ordine_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('ordine_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['ordine_id'] = $ordine_id;

        return $this;
    }

    /**
     * Gets bene_servizio_ordine_id
     *
     * @return int|null
     */
    public function getBeneServizioOrdineId()
    {
        return $this->container['bene_servizio_ordine_id'];
    }

    /**
     * Sets bene_servizio_ordine_id
     *
     * @param int|null $bene_servizio_ordine_id Identificativo univoco del `BeneServizio` (bene o servizio) all'interno dell'ordine referenziato da `OrdineId`.
     *
     * @return self
     */
    public function setBeneServizioOrdineId($bene_servizio_ordine_id)
    {
        if (is_null($bene_servizio_ordine_id)) {
            array_push($this->openAPINullablesSetToNull, 'bene_servizio_ordine_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('bene_servizio_ordine_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['bene_servizio_ordine_id'] = $bene_servizio_ordine_id;

        return $this;
    }

    /**
     * Gets taglia_id
     *
     * @return int|null
     */
    public function getTagliaId()
    {
        return $this->container['taglia_id'];
    }

    /**
     * Sets taglia_id
     *
     * @param int|null $taglia_id Identificativo univoco della `Taglia` (taglia).
     *
     * @return self
     */
    public function setTagliaId($taglia_id)
    {
        if (is_null($taglia_id)) {
            array_push($this->openAPINullablesSetToNull, 'taglia_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('taglia_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['taglia_id'] = $taglia_id;

        return $this;
    }

    /**
     * Gets taglia
     *
     * @return \Amica\Sdk\Model\Taglia|null
     */
    public function getTaglia()
    {
        return $this->container['taglia'];
    }

    /**
     * Sets taglia
     *
     * @param \Amica\Sdk\Model\Taglia|null $taglia taglia
     *
     * @return self
     */
    public function setTaglia($taglia)
    {
        if (is_null($taglia)) {
            throw new \InvalidArgumentException('non-nullable taglia cannot be null');
        }
        $this->container['taglia'] = $taglia;

        return $this;
    }

    /**
     * Gets ritenuta_fiscale
     *
     * @return bool|null
     */
    public function getRitenutaFiscale()
    {
        return $this->container['ritenuta_fiscale'];
    }

    /**
     * Sets ritenuta_fiscale
     *
     * @param bool|null $ritenuta_fiscale Indica se al bene o servizio deve essere applicata una ritenuta fiscale. `true` se deve essere applicata, `false` altrimenti.
     *
     * @return self
     */
    public function setRitenutaFiscale($ritenuta_fiscale)
    {
        if (is_null($ritenuta_fiscale)) {
            throw new \InvalidArgumentException('non-nullable ritenuta_fiscale cannot be null');
        }
        $this->container['ritenuta_fiscale'] = $ritenuta_fiscale;

        return $this;
    }

    /**
     * Gets posizione
     *
     * @return int|null
     */
    public function getPosizione()
    {
        return $this->container['posizione'];
    }

    /**
     * Sets posizione
     *
     * @param int|null $posizione Indice di riga all'interno del documento. Questa proprietà è utilizzata solo dal client desktop Amica 20 per Windows e deve essere considerata obsoleta.
     *
     * @return self
     */
    public function setPosizione($posizione)
    {
        if (is_null($posizione)) {
            throw new \InvalidArgumentException('non-nullable posizione cannot be null');
        }
        $this->container['posizione'] = $posizione;

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
     * @param string|null $codice Codice del bene o servizio.
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
        if (!is_null($codice) && (mb_strlen($codice) > 30)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling BeneServizio., must be smaller than or equal to 30.');
        }
        if (!is_null($codice) && (mb_strlen($codice) < 0)) {
            throw new \InvalidArgumentException('invalid length for $codice when calling BeneServizio., must be bigger than or equal to 0.');
        }

        $this->container['codice'] = $codice;

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
     * @param string|null $descrizione Descrizione. Obbligatoria, tranne che sulle righe di totale per aliquota create dall'importazione dei DDT con `Totali`: hanno `CausaleIvaId` e `Importo` ma niente `ArticoloId` né descrizione, e la fattura elettronica usa come loro descrizione il riferimento al DDT che le precede.
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
        $this->container['descrizione'] = $descrizione;

        return $this;
    }

    /**
     * Gets unit_misura
     *
     * @return string|null
     */
    public function getUnitMisura()
    {
        return $this->container['unit_misura'];
    }

    /**
     * Sets unit_misura
     *
     * @param string|null $unit_misura Unità di misura.
     *
     * @return self
     */
    public function setUnitMisura($unit_misura)
    {
        if (is_null($unit_misura)) {
            array_push($this->openAPINullablesSetToNull, 'unit_misura');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('unit_misura', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($unit_misura) && (mb_strlen($unit_misura) > 15)) {
            throw new \InvalidArgumentException('invalid length for $unit_misura when calling BeneServizio., must be smaller than or equal to 15.');
        }
        if (!is_null($unit_misura) && (mb_strlen($unit_misura) < 0)) {
            throw new \InvalidArgumentException('invalid length for $unit_misura when calling BeneServizio., must be bigger than or equal to 0.');
        }

        $this->container['unit_misura'] = $unit_misura;

        return $this;
    }

    /**
     * Gets quantit
     *
     * @return float|null
     */
    public function getQuantit()
    {
        return $this->container['quantit'];
    }

    /**
     * Sets quantit
     *
     * @param float|null $quantit Quantità movimentata.
     *
     * @return self
     */
    public function setQuantit($quantit)
    {
        if (is_null($quantit)) {
            array_push($this->openAPINullablesSetToNull, 'quantit');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('quantit', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['quantit'] = $quantit;

        return $this;
    }

    /**
     * Gets quantit_evasa
     *
     * @return float|null
     */
    public function getQuantitEvasa()
    {
        return $this->container['quantit_evasa'];
    }

    /**
     * Sets quantit_evasa
     *
     * @param float|null $quantit_evasa Quantità dell'ordine evasa. Questo valore viene aggiornato automaticamente quando viene emessa una fattura o un DDT che contiene un riferimento a un ordine ed è attivo <a href=\"https://gestionaleamica.com/Software/GestioneOrdiniPreventivi\">Ordini e Preventivi</a>.
     *
     * @return self
     */
    public function setQuantitEvasa($quantit_evasa)
    {
        if (is_null($quantit_evasa)) {
            throw new \InvalidArgumentException('non-nullable quantit_evasa cannot be null');
        }
        $this->container['quantit_evasa'] = $quantit_evasa;

        return $this;
    }

    /**
     * Gets quantit_da_evadere
     *
     * @return float|null
     */
    public function getQuantitDaEvadere()
    {
        return $this->container['quantit_da_evadere'];
    }

    /**
     * Sets quantit_da_evadere
     *
     * @param float|null $quantit_da_evadere Quantità che resta da evadere.
     *
     * @return self
     */
    public function setQuantitDaEvadere($quantit_da_evadere)
    {
        if (is_null($quantit_da_evadere)) {
            throw new \InvalidArgumentException('non-nullable quantit_da_evadere cannot be null');
        }
        $this->container['quantit_da_evadere'] = $quantit_da_evadere;

        return $this;
    }

    /**
     * Gets sconto1
     *
     * @return float|null
     */
    public function getSconto1()
    {
        return $this->container['sconto1'];
    }

    /**
     * Sets sconto1
     *
     * @param float|null $sconto1 Primo sconto percentuale, applicato al prezzo del bene o servizio. Il valore è una frazione: `0.05` corrisponde al 5%.
     *
     * @return self
     */
    public function setSconto1($sconto1)
    {
        if (is_null($sconto1)) {
            array_push($this->openAPINullablesSetToNull, 'sconto1');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto1', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto1'] = $sconto1;

        return $this;
    }

    /**
     * Gets sconto2
     *
     * @return float|null
     */
    public function getSconto2()
    {
        return $this->container['sconto2'];
    }

    /**
     * Sets sconto2
     *
     * @param float|null $sconto2 Secondo sconto percentuale, applicato dopo l'applicazione di `Sconto1` Il valore è una frazione: `0.03` corrisponde al 3%.
     *
     * @return self
     */
    public function setSconto2($sconto2)
    {
        if (is_null($sconto2)) {
            array_push($this->openAPINullablesSetToNull, 'sconto2');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto2', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto2'] = $sconto2;

        return $this;
    }

    /**
     * Gets sconto3
     *
     * @return float|null
     */
    public function getSconto3()
    {
        return $this->container['sconto3'];
    }

    /**
     * Sets sconto3
     *
     * @param float|null $sconto3 Terzo sconto percentuale, applicato dopo l'applicazione di `Sconto2` Il valore è una frazione: `0.02` corrisponde al 2%.
     *
     * @return self
     */
    public function setSconto3($sconto3)
    {
        if (is_null($sconto3)) {
            array_push($this->openAPINullablesSetToNull, 'sconto3');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto3', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto3'] = $sconto3;

        return $this;
    }

    /**
     * Gets sconto4
     *
     * @return float|null
     */
    public function getSconto4()
    {
        return $this->container['sconto4'];
    }

    /**
     * Sets sconto4
     *
     * @param float|null $sconto4 Quarto sconto percentuale, applicato dopo l'applicazione di `Sconto3` Il valore è una frazione: `0.01` corrisponde all'1%.
     *
     * @return self
     */
    public function setSconto4($sconto4)
    {
        if (is_null($sconto4)) {
            array_push($this->openAPINullablesSetToNull, 'sconto4');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto4', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto4'] = $sconto4;

        return $this;
    }

    /**
     * Gets sconto_incondizionato
     *
     * @return float|null
     */
    public function getScontoIncondizionato()
    {
        return $this->container['sconto_incondizionato'];
    }

    /**
     * Sets sconto_incondizionato
     *
     * @param float|null $sconto_incondizionato Sconto incondizionato, in valuta, applicato al prezzo del bene o servizio.
     *
     * @return self
     */
    public function setScontoIncondizionato($sconto_incondizionato)
    {
        if (is_null($sconto_incondizionato)) {
            array_push($this->openAPINullablesSetToNull, 'sconto_incondizionato');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto_incondizionato', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto_incondizionato'] = $sconto_incondizionato;

        return $this;
    }

    /**
     * Gets provvigione_agente
     *
     * @return float|null
     */
    public function getProvvigioneAgente()
    {
        return $this->container['provvigione_agente'];
    }

    /**
     * Sets provvigione_agente
     *
     * @param float|null $provvigione_agente Provvigione corrisposta all'agente, in percentuale.
     *
     * @return self
     */
    public function setProvvigioneAgente($provvigione_agente)
    {
        if (is_null($provvigione_agente)) {
            array_push($this->openAPINullablesSetToNull, 'provvigione_agente');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provvigione_agente', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['provvigione_agente'] = $provvigione_agente;

        return $this;
    }

    /**
     * Gets provvigione_capo_area
     *
     * @return float|null
     */
    public function getProvvigioneCapoArea()
    {
        return $this->container['provvigione_capo_area'];
    }

    /**
     * Sets provvigione_capo_area
     *
     * @param float|null $provvigione_capo_area Provvigione corrisposta al capo area, in percentuale.
     *
     * @return self
     */
    public function setProvvigioneCapoArea($provvigione_capo_area)
    {
        if (is_null($provvigione_capo_area)) {
            array_push($this->openAPINullablesSetToNull, 'provvigione_capo_area');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('provvigione_capo_area', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['provvigione_capo_area'] = $provvigione_capo_area;

        return $this;
    }

    /**
     * Gets prezzo_id
     *
     * @return int|null
     */
    public function getPrezzoId()
    {
        return $this->container['prezzo_id'];
    }

    /**
     * Sets prezzo_id
     *
     * @param int|null $prezzo_id Identificativo univoco del `Prezzo` (prezzo).
     *
     * @return self
     */
    public function setPrezzoId($prezzo_id)
    {
        if (is_null($prezzo_id)) {
            array_push($this->openAPINullablesSetToNull, 'prezzo_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prezzo_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prezzo_id'] = $prezzo_id;

        return $this;
    }

    /**
     * Gets prezzo
     *
     * @return float|null
     */
    public function getPrezzo()
    {
        return $this->container['prezzo'];
    }

    /**
     * Sets prezzo
     *
     * @param float|null $prezzo Prezzo.
     *
     * @return self
     */
    public function setPrezzo($prezzo)
    {
        if (is_null($prezzo)) {
            array_push($this->openAPINullablesSetToNull, 'prezzo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prezzo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prezzo'] = $prezzo;

        return $this;
    }

    /**
     * Gets prezzo_scontato
     *
     * @return float|null
     */
    public function getPrezzoScontato()
    {
        return $this->container['prezzo_scontato'];
    }

    /**
     * Sets prezzo_scontato
     *
     * @param float|null $prezzo_scontato Prezzo scontato, cioè al netto degli sconti percentuali indicati e dello sconto incondizionato.
     *
     * @return self
     */
    public function setPrezzoScontato($prezzo_scontato)
    {
        if (is_null($prezzo_scontato)) {
            array_push($this->openAPINullablesSetToNull, 'prezzo_scontato');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prezzo_scontato', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prezzo_scontato'] = $prezzo_scontato;

        return $this;
    }

    /**
     * Gets prezzo_ivato
     *
     * @return float|null
     */
    public function getPrezzoIvato()
    {
        return $this->container['prezzo_ivato'];
    }

    /**
     * Sets prezzo_ivato
     *
     * @param float|null $prezzo_ivato Prezzo comprensivo di IVA.
     *
     * @return self
     */
    public function setPrezzoIvato($prezzo_ivato)
    {
        if (is_null($prezzo_ivato)) {
            array_push($this->openAPINullablesSetToNull, 'prezzo_ivato');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('prezzo_ivato', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['prezzo_ivato'] = $prezzo_ivato;

        return $this;
    }

    /**
     * Gets importo
     *
     * @return float|null
     */
    public function getImporto()
    {
        return $this->container['importo'];
    }

    /**
     * Sets importo
     *
     * @param float|null $importo Importo, determinato come prodotto di `Quantità` e `PrezzoScontato`.
     *
     * @return self
     */
    public function setImporto($importo)
    {
        if (is_null($importo)) {
            array_push($this->openAPINullablesSetToNull, 'importo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('importo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['importo'] = $importo;

        return $this;
    }

    /**
     * Gets importo_ivato
     *
     * @return float|null
     */
    public function getImportoIvato()
    {
        return $this->container['importo_ivato'];
    }

    /**
     * Sets importo_ivato
     *
     * @param float|null $importo_ivato Importo IVA inclusa, calcolato dalla API in lettura come `beneservizio/compute` a partire dai prezzi salvati e dal flag `IvaEsclusa` del documento. Non viene memorizzato: in `POST` e `PUT` è ignorato. Con IVA inclusa è la base dell'imposta del riepilogo, da passare nella riga di `documento/compute` (`ImportoIvato`). Assente sulle righe senza prezzo.
     *
     * @return self
     */
    public function setImportoIvato($importo_ivato)
    {
        if (is_null($importo_ivato)) {
            array_push($this->openAPINullablesSetToNull, 'importo_ivato');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('importo_ivato', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['importo_ivato'] = $importo_ivato;

        return $this;
    }

    /**
     * Gets dati_aggiuntivi
     *
     * @return \Amica\Sdk\Model\DatiAggiuntivi|null
     */
    public function getDatiAggiuntivi()
    {
        return $this->container['dati_aggiuntivi'];
    }

    /**
     * Sets dati_aggiuntivi
     *
     * @param \Amica\Sdk\Model\DatiAggiuntivi|null $dati_aggiuntivi dati_aggiuntivi
     *
     * @return self
     */
    public function setDatiAggiuntivi($dati_aggiuntivi)
    {
        if (is_null($dati_aggiuntivi)) {
            throw new \InvalidArgumentException('non-nullable dati_aggiuntivi cannot be null');
        }
        $this->container['dati_aggiuntivi'] = $dati_aggiuntivi;

        return $this;
    }

    /**
     * Gets sconto
     *
     * @return string|null
     */
    public function getSconto()
    {
        return $this->container['sconto'];
    }

    /**
     * Sets sconto
     *
     * @param string|null $sconto Descrizione di tutti gli sconti combinati (es: \"10,5%+5€\").
     *
     * @return self
     */
    public function setSconto($sconto)
    {
        if (is_null($sconto)) {
            array_push($this->openAPINullablesSetToNull, 'sconto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('sconto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['sconto'] = $sconto;

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


