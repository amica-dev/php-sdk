<?php
/**
 * PeriodicInvoicingData
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
 * PeriodicInvoicingData Class Doc Comment
 *
 * @category Class
 * @description Dati di fatturazione periodica.
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class PeriodicInvoicingData implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'PeriodicInvoicingData';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'da' => '\DateTime',
        'a' => '\DateTime',
        'tipo_importazione' => 'string',
        'causale_documento_id' => 'int',
        'data_documento' => '\DateTime',
        'tipo_documento' => 'string'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'da' => 'date-time',
        'a' => 'date-time',
        'tipo_importazione' => null,
        'causale_documento_id' => 'int32',
        'data_documento' => 'date-time',
        'tipo_documento' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'da' => false,
        'a' => false,
        'tipo_importazione' => false,
        'causale_documento_id' => true,
        'data_documento' => false,
        'tipo_documento' => false
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
        'da' => 'da',
        'a' => 'a',
        'tipo_importazione' => 'tipoImportazione',
        'causale_documento_id' => 'causaleDocumentoId',
        'data_documento' => 'dataDocumento',
        'tipo_documento' => 'tipoDocumento'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'da' => 'setDa',
        'a' => 'setA',
        'tipo_importazione' => 'setTipoImportazione',
        'causale_documento_id' => 'setCausaleDocumentoId',
        'data_documento' => 'setDataDocumento',
        'tipo_documento' => 'setTipoDocumento'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'da' => 'getDa',
        'a' => 'getA',
        'tipo_importazione' => 'getTipoImportazione',
        'causale_documento_id' => 'getCausaleDocumentoId',
        'data_documento' => 'getDataDocumento',
        'tipo_documento' => 'getTipoDocumento'
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

    public const TIPO_IMPORTAZIONE_RIGHE = 'Righe';
    public const TIPO_IMPORTAZIONE_TOTALI = 'Totali';
    public const TIPO_IMPORTAZIONE_ACCORPA_ARTICOLI = 'AccorpaArticoli';
    public const TIPO_DOCUMENTO_DDT = 'Ddt';
    public const TIPO_DOCUMENTO_FATTURA_ACCOMPAGNATORIA = 'FatturaAccompagnatoria';
    public const TIPO_DOCUMENTO_FATTURA_ACCONTO = 'FatturaAcconto';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA = 'FatturaDifferita';
    public const TIPO_DOCUMENTO_FATTURA_PROFESSIONISTA = 'FatturaProfessionista';
    public const TIPO_DOCUMENTO_RICEVUTA_FISCALE = 'RicevutaFiscale';
    public const TIPO_DOCUMENTO_NOTA_CREDITO = 'NotaCredito';
    public const TIPO_DOCUMENTO_NOTA_DEBITO = 'NotaDebito';
    public const TIPO_DOCUMENTO_SCONTRINO = 'Scontrino';
    public const TIPO_DOCUMENTO_ORDINE_CLIENTE = 'OrdineCliente';
    public const TIPO_DOCUMENTO_PREVENTIVO = 'Preventivo';
    public const TIPO_DOCUMENTO_DDT_FORNITORE = 'DdtFornitore';
    public const TIPO_DOCUMENTO_FATTURA_ACCOMPAGNATORIA_FORNITORE = 'FatturaAccompagnatoriaFornitore';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA_FORNITORE = 'FatturaDifferitaFornitore';
    public const TIPO_DOCUMENTO_FATTURA_PROFESSIONISTA_FORNITORE = 'FatturaProfessionistaFornitore';
    public const TIPO_DOCUMENTO_NOTA_CREDITO_FORNITORE = 'NotaCreditoFornitore';
    public const TIPO_DOCUMENTO_NOTA_DEBITO_FORNITORE = 'NotaDebitoFornitore';
    public const TIPO_DOCUMENTO_PREVENTIVO_FORNITORE = 'PreventivoFornitore';
    public const TIPO_DOCUMENTO_ORDINE_FORNITORE = 'OrdineFornitore';
    public const TIPO_DOCUMENTO_RESO_CLIENTE = 'ResoCliente';
    public const TIPO_DOCUMENTO_FATTURA_ACCONTO_FORNITORE = 'FatturaAccontoFornitore';
    public const TIPO_DOCUMENTO_FATTURA_PRO_FORMA = 'FatturaProForma';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_FATTURA_REVERSE_CHARGE_INTERNO = 'IntegrazioneFatturaReverseChargeInterno';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_SERVIZI_ESTERO = 'IntegrazioneAcquistoServiziEstero';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_INTRACOMUNITARI = 'IntegrazioneAcquistoBeniIntracomunitari';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_ART17 = 'IntegrazioneAcquistoBeniArt17';
    public const TIPO_DOCUMENTO_AUTOFATTURA_REGOLARIZZAZIONE_FATTURE = 'AutofatturaRegolarizzazioneFatture';
    public const TIPO_DOCUMENTO_AUTOFATTURA_SPLAFONAMENTO = 'AutofatturaSplafonamento';
    public const TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA = 'EstrazioneBeniDepositoIva';
    public const TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_CON_VERSAMENTO_IVA = 'EstrazioneBeniDepositoIvaConVersamentoIva';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_A = 'FatturaDifferitaArt21Comma4LetteraA';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_B = 'FatturaDifferitaArt21Comma4LetteraB';
    public const TIPO_DOCUMENTO_CESSIONI_BENI_AMMORTIZZABILI_E_PASSAGGI_INTERNI = 'CessioniBeniAmmortizzabiliEPassaggiInterni';
    public const TIPO_DOCUMENTO_FATTURA_AUTOCONSUMO_O_CESSIONI_GRATUITE = 'FatturaAutoconsumoOCessioniGratuite';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_FATTURA_REVERSE_CHARGE_INTERNO_FORNITORE = 'IntegrazioneFatturaReverseChargeInternoFornitore';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_SERVIZI_ESTERO_FORNITORE = 'IntegrazioneAcquistoServiziEsteroFornitore';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_INTRACOMUNITARI_FORNITORE = 'IntegrazioneAcquistoBeniIntracomunitariFornitore';
    public const TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_ART17_FORNITORE = 'IntegrazioneAcquistoBeniArt17Fornitore';
    public const TIPO_DOCUMENTO_AUTOFATTURA_REGOLARIZZAZIONE_FATTURE_FORNITORE = 'AutofatturaRegolarizzazioneFattureFornitore';
    public const TIPO_DOCUMENTO_AUTOFATTURA_SPLAFONAMENTO_FORNITORE = 'AutofatturaSplafonamentoFornitore';
    public const TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_FORNITORE = 'EstrazioneBeniDepositoIvaFornitore';
    public const TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_CON_VERSAMENTO_IVA_FORNITORE = 'EstrazioneBeniDepositoIvaConVersamentoIvaFornitore';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_A_FORNITORE = 'FatturaDifferitaArt21Comma4LetteraAFornitore';
    public const TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_B_FORNITORE = 'FatturaDifferitaArt21Comma4LetteraBFornitore';
    public const TIPO_DOCUMENTO_CESSIONI_BENI_AMMORTIZZABILI_E_PASSAGGI_INTERNI_FORNITORE = 'CessioniBeniAmmortizzabiliEPassaggiInterniFornitore';
    public const TIPO_DOCUMENTO_FATTURA_AUTOCONSUMO_O_CESSIONI_GRATUITE_FORNITORE = 'FatturaAutoconsumoOCessioniGratuiteFornitore';
    public const TIPO_DOCUMENTO_ACQUISTO_SAN_MARINO_CON_IVA = 'AcquistoSanMarinoConIva';
    public const TIPO_DOCUMENTO_COMUNICAZIONE_OMESSA_O_IRREGOLARE_FATTURAZIONE = 'ComunicazioneOmessaOIrregolareFatturazione';
    public const TIPO_DOCUMENTO_COMUNICAZIONE_OMESSA_O_IRREGOLARE_FATTURAZIONE_FORNITORE = 'ComunicazioneOmessaOIrregolareFatturazioneFornitore';

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoImportazioneAllowableValues()
    {
        return [
            self::TIPO_IMPORTAZIONE_RIGHE,
            self::TIPO_IMPORTAZIONE_TOTALI,
            self::TIPO_IMPORTAZIONE_ACCORPA_ARTICOLI,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getTipoDocumentoAllowableValues()
    {
        return [
            self::TIPO_DOCUMENTO_DDT,
            self::TIPO_DOCUMENTO_FATTURA_ACCOMPAGNATORIA,
            self::TIPO_DOCUMENTO_FATTURA_ACCONTO,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA,
            self::TIPO_DOCUMENTO_FATTURA_PROFESSIONISTA,
            self::TIPO_DOCUMENTO_RICEVUTA_FISCALE,
            self::TIPO_DOCUMENTO_NOTA_CREDITO,
            self::TIPO_DOCUMENTO_NOTA_DEBITO,
            self::TIPO_DOCUMENTO_SCONTRINO,
            self::TIPO_DOCUMENTO_ORDINE_CLIENTE,
            self::TIPO_DOCUMENTO_PREVENTIVO,
            self::TIPO_DOCUMENTO_DDT_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_ACCOMPAGNATORIA_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_PROFESSIONISTA_FORNITORE,
            self::TIPO_DOCUMENTO_NOTA_CREDITO_FORNITORE,
            self::TIPO_DOCUMENTO_NOTA_DEBITO_FORNITORE,
            self::TIPO_DOCUMENTO_PREVENTIVO_FORNITORE,
            self::TIPO_DOCUMENTO_ORDINE_FORNITORE,
            self::TIPO_DOCUMENTO_RESO_CLIENTE,
            self::TIPO_DOCUMENTO_FATTURA_ACCONTO_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_PRO_FORMA,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_FATTURA_REVERSE_CHARGE_INTERNO,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_SERVIZI_ESTERO,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_INTRACOMUNITARI,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_ART17,
            self::TIPO_DOCUMENTO_AUTOFATTURA_REGOLARIZZAZIONE_FATTURE,
            self::TIPO_DOCUMENTO_AUTOFATTURA_SPLAFONAMENTO,
            self::TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA,
            self::TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_CON_VERSAMENTO_IVA,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_A,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_B,
            self::TIPO_DOCUMENTO_CESSIONI_BENI_AMMORTIZZABILI_E_PASSAGGI_INTERNI,
            self::TIPO_DOCUMENTO_FATTURA_AUTOCONSUMO_O_CESSIONI_GRATUITE,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_FATTURA_REVERSE_CHARGE_INTERNO_FORNITORE,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_SERVIZI_ESTERO_FORNITORE,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_INTRACOMUNITARI_FORNITORE,
            self::TIPO_DOCUMENTO_INTEGRAZIONE_ACQUISTO_BENI_ART17_FORNITORE,
            self::TIPO_DOCUMENTO_AUTOFATTURA_REGOLARIZZAZIONE_FATTURE_FORNITORE,
            self::TIPO_DOCUMENTO_AUTOFATTURA_SPLAFONAMENTO_FORNITORE,
            self::TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_FORNITORE,
            self::TIPO_DOCUMENTO_ESTRAZIONE_BENI_DEPOSITO_IVA_CON_VERSAMENTO_IVA_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_A_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_DIFFERITA_ART21_COMMA4_LETTERA_B_FORNITORE,
            self::TIPO_DOCUMENTO_CESSIONI_BENI_AMMORTIZZABILI_E_PASSAGGI_INTERNI_FORNITORE,
            self::TIPO_DOCUMENTO_FATTURA_AUTOCONSUMO_O_CESSIONI_GRATUITE_FORNITORE,
            self::TIPO_DOCUMENTO_ACQUISTO_SAN_MARINO_CON_IVA,
            self::TIPO_DOCUMENTO_COMUNICAZIONE_OMESSA_O_IRREGOLARE_FATTURAZIONE,
            self::TIPO_DOCUMENTO_COMUNICAZIONE_OMESSA_O_IRREGOLARE_FATTURAZIONE_FORNITORE,
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
        $this->setIfExists('da', $data ?? [], null);
        $this->setIfExists('a', $data ?? [], null);
        $this->setIfExists('tipo_importazione', $data ?? [], null);
        $this->setIfExists('causale_documento_id', $data ?? [], null);
        $this->setIfExists('data_documento', $data ?? [], null);
        $this->setIfExists('tipo_documento', $data ?? [], null);
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

        $allowedValues = $this->getTipoImportazioneAllowableValues();
        if (!is_null($this->container['tipo_importazione']) && !in_array($this->container['tipo_importazione'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_importazione', must be one of '%s'",
                $this->container['tipo_importazione'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getTipoDocumentoAllowableValues();
        if (!is_null($this->container['tipo_documento']) && !in_array($this->container['tipo_documento'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_documento', must be one of '%s'",
                $this->container['tipo_documento'],
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
     * Gets da
     *
     * @return \DateTime|null
     */
    public function getDa()
    {
        return $this->container['da'];
    }

    /**
     * Sets da
     *
     * @param \DateTime|null $da Data di inizio del periodo.
     *
     * @return self
     */
    public function setDa($da)
    {
        if (is_null($da)) {
            throw new \InvalidArgumentException('non-nullable da cannot be null');
        }
        $this->container['da'] = $da;

        return $this;
    }

    /**
     * Gets a
     *
     * @return \DateTime|null
     */
    public function getA()
    {
        return $this->container['a'];
    }

    /**
     * Sets a
     *
     * @param \DateTime|null $a Data di fine del periodo.
     *
     * @return self
     */
    public function setA($a)
    {
        if (is_null($a)) {
            throw new \InvalidArgumentException('non-nullable a cannot be null');
        }
        $this->container['a'] = $a;

        return $this;
    }

    /**
     * Gets tipo_importazione
     *
     * @return string|null
     */
    public function getTipoImportazione()
    {
        return $this->container['tipo_importazione'];
    }

    /**
     * Sets tipo_importazione
     *
     * @param string|null $tipo_importazione Tipo di importazione.
     *
     * @return self
     */
    public function setTipoImportazione($tipo_importazione)
    {
        if (is_null($tipo_importazione)) {
            throw new \InvalidArgumentException('non-nullable tipo_importazione cannot be null');
        }
        $allowedValues = $this->getTipoImportazioneAllowableValues();
        if (!in_array($tipo_importazione, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_importazione', must be one of '%s'",
                    $tipo_importazione,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_importazione'] = $tipo_importazione;

        return $this;
    }

    /**
     * Gets causale_documento_id
     *
     * @return int|null
     */
    public function getCausaleDocumentoId()
    {
        return $this->container['causale_documento_id'];
    }

    /**
     * Sets causale_documento_id
     *
     * @param int|null $causale_documento_id Identificativo univoco della `CausaleDocumento` del documento generato.
     *
     * @return self
     */
    public function setCausaleDocumentoId($causale_documento_id)
    {
        if (is_null($causale_documento_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_documento_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_documento_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_documento_id'] = $causale_documento_id;

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
     * @param \DateTime|null $data_documento Data del documento generato.
     *
     * @return self
     */
    public function setDataDocumento($data_documento)
    {
        if (is_null($data_documento)) {
            throw new \InvalidArgumentException('non-nullable data_documento cannot be null');
        }
        $this->container['data_documento'] = $data_documento;

        return $this;
    }

    /**
     * Gets tipo_documento
     *
     * @return string|null
     */
    public function getTipoDocumento()
    {
        return $this->container['tipo_documento'];
    }

    /**
     * Sets tipo_documento
     *
     * @param string|null $tipo_documento Tipo del documento generato.
     *
     * @return self
     */
    public function setTipoDocumento($tipo_documento)
    {
        if (is_null($tipo_documento)) {
            throw new \InvalidArgumentException('non-nullable tipo_documento cannot be null');
        }
        $allowedValues = $this->getTipoDocumentoAllowableValues();
        if (!in_array($tipo_documento, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'tipo_documento', must be one of '%s'",
                    $tipo_documento,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['tipo_documento'] = $tipo_documento;

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


