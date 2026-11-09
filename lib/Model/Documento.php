<?php
/**
 * Documento
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
 * Documento Class Doc Comment
 *
 * @category Class
 * @description Un documento (fattura, documento di trasporto, ecc.)
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class Documento implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Documento';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'concurrency_token' => 'int',
        'anagrafica_id' => 'int',
        'anagrafica' => '\Amica\Sdk\Model\Anagrafica',
        'causale_documento_id' => 'int',
        'causale_documento' => '\Amica\Sdk\Model\CausaleDocumento',
        'agente_id' => 'int',
        'agente' => '\Amica\Sdk\Model\Anagrafica',
        'pagamento_id' => 'int',
        'pagamento' => '\Amica\Sdk\Model\Pagamento',
        'vettore_id' => 'int',
        'vettore' => '\Amica\Sdk\Model\Anagrafica',
        'cassa_previdenziale_causale_iva_id' => 'int',
        'modello_id' => 'int',
        'cassa_previdenziale_causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'destinazione_id' => 'int',
        'destinazione' => '\Amica\Sdk\Model\Indirizzo',
        'valuta_id' => 'int',
        'valuta' => '\Amica\Sdk\Model\Valuta',
        'causale_magazzino_id' => 'int',
        'causale_magazzino' => '\Amica\Sdk\Model\CausaleMagazzino',
        'iva1_causale_iva_id' => 'int',
        'iva1_causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'iva2_causale_iva_id' => 'int',
        'iva2_causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'iva3_causale_iva_id' => 'int',
        'iva3_causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'iva4_causale_iva_id' => 'int',
        'iva4_causale_iva' => '\Amica\Sdk\Model\CausaleIva',
        'tipo_documento' => 'string',
        'documento_fiscale' => 'string',
        'destinazione_diversa' => 'bool',
        'iva_esclusa' => 'bool',
        'ritenuta_include_cassa_previdenziale' => 'bool',
        'classe_documento' => 'string',
        'contatto' => 'int',
        'numero_parte_testo' => 'string',
        'numero_parte_numerica' => 'int',
        'data' => '\DateTime',
        'stato' => 'string',
        'data_scadenza_preventivo' => '\DateTime',
        'data_inizio_scadenze' => '\DateTime',
        'sconto_percentuale' => 'float',
        'sconto_incondizionato_importo' => 'float',
        'abbuono_importo' => 'float',
        'ritenuta_percentuale' => 'float',
        'enasarco_percentuale' => 'float',
        'enasarco_importo' => 'float',
        'quota_ritenuta_su_imponibile' => 'float',
        'cambio' => 'float',
        'cassa_previdenziale_nome' => 'string',
        'cassa_previdenziale_percentuale' => 'float',
        'note' => 'string',
        'sconto_pagamento_percentuale' => 'float',
        'iva1_imponibile' => 'float',
        'iva2_imponibile' => 'float',
        'iva3_imponibile' => 'float',
        'iva4_imponibile' => 'float',
        'iva1_imponibile_lordo' => 'float',
        'iva2_imponibile_lordo' => 'float',
        'iva3_imponibile_lordo' => 'float',
        'iva4_imponibile_lordo' => 'float',
        'iva1_imposta' => 'float',
        'iva2_imposta' => 'float',
        'iva3_imposta' => 'float',
        'iva4_imposta' => 'float',
        'totale_ritenuta' => 'float',
        'totale_cassa_previdenziale' => 'float',
        'totale_spese' => 'float',
        'totale_spese_esenti' => 'float',
        'totale_spese_non_esenti' => 'float',
        'totale_merce' => 'float',
        'totale_merce_scontata' => 'float',
        'totale_imponibile' => 'float',
        'totale_imposta' => 'float',
        'totale_esente' => 'float',
        'totale_documento' => 'float',
        'netto_a_pagare' => 'float',
        'totale_split_payment' => 'float',
        'bollo' => 'float',
        'xml_file_name' => 'string',
        'riferimento_amministrazione' => 'string',
        'banca' => '\Amica\Sdk\Model\BancaDocumento',
        'dati_trasporto' => '\Amica\Sdk\Model\DatiTrasporto',
        'dati_sdi' => '\Amica\Sdk\Model\DatiSdi',
        'dati_pa' => '\Amica\Sdk\Model\DatiPA',
        'righe' => '\Amica\Sdk\Model\BeneServizio[]',
        'spese_documento' => '\Amica\Sdk\Model\SpesaDocumento[]',
        'scadenze' => '\Amica\Sdk\Model\Scadenza[]',
        'numero' => 'string',
        'media_file' => '\Amica\Sdk\Model\DocumentoMediaFile',
        'allegato' => 'string',
        'bonus_fiscale_causale' => 'string',
        'bonus_fiscale_importo' => 'float',
        'bonus_fiscale_percentuale' => 'float',
        'progressivo_invio' => '\Amica\Sdk\Model\ProgressivoInvio[]'
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
        'anagrafica_id' => 'int32',
        'anagrafica' => null,
        'causale_documento_id' => 'int32',
        'causale_documento' => null,
        'agente_id' => 'int32',
        'agente' => null,
        'pagamento_id' => 'int32',
        'pagamento' => null,
        'vettore_id' => 'int32',
        'vettore' => null,
        'cassa_previdenziale_causale_iva_id' => 'int32',
        'modello_id' => 'int32',
        'cassa_previdenziale_causale_iva' => null,
        'destinazione_id' => 'int32',
        'destinazione' => null,
        'valuta_id' => 'int32',
        'valuta' => null,
        'causale_magazzino_id' => 'int32',
        'causale_magazzino' => null,
        'iva1_causale_iva_id' => 'int32',
        'iva1_causale_iva' => null,
        'iva2_causale_iva_id' => 'int32',
        'iva2_causale_iva' => null,
        'iva3_causale_iva_id' => 'int32',
        'iva3_causale_iva' => null,
        'iva4_causale_iva_id' => 'int32',
        'iva4_causale_iva' => null,
        'tipo_documento' => null,
        'documento_fiscale' => null,
        'destinazione_diversa' => null,
        'iva_esclusa' => null,
        'ritenuta_include_cassa_previdenziale' => null,
        'classe_documento' => null,
        'contatto' => 'int32',
        'numero_parte_testo' => null,
        'numero_parte_numerica' => 'int32',
        'data' => 'date-time',
        'stato' => null,
        'data_scadenza_preventivo' => 'date-time',
        'data_inizio_scadenze' => 'date-time',
        'sconto_percentuale' => 'double',
        'sconto_incondizionato_importo' => 'double',
        'abbuono_importo' => 'double',
        'ritenuta_percentuale' => 'double',
        'enasarco_percentuale' => 'double',
        'enasarco_importo' => 'double',
        'quota_ritenuta_su_imponibile' => 'double',
        'cambio' => 'double',
        'cassa_previdenziale_nome' => null,
        'cassa_previdenziale_percentuale' => 'double',
        'note' => null,
        'sconto_pagamento_percentuale' => 'double',
        'iva1_imponibile' => 'double',
        'iva2_imponibile' => 'double',
        'iva3_imponibile' => 'double',
        'iva4_imponibile' => 'double',
        'iva1_imponibile_lordo' => 'double',
        'iva2_imponibile_lordo' => 'double',
        'iva3_imponibile_lordo' => 'double',
        'iva4_imponibile_lordo' => 'double',
        'iva1_imposta' => 'double',
        'iva2_imposta' => 'double',
        'iva3_imposta' => 'double',
        'iva4_imposta' => 'double',
        'totale_ritenuta' => 'double',
        'totale_cassa_previdenziale' => 'double',
        'totale_spese' => 'double',
        'totale_spese_esenti' => 'double',
        'totale_spese_non_esenti' => 'double',
        'totale_merce' => 'double',
        'totale_merce_scontata' => 'double',
        'totale_imponibile' => 'double',
        'totale_imposta' => 'double',
        'totale_esente' => 'double',
        'totale_documento' => 'double',
        'netto_a_pagare' => 'double',
        'totale_split_payment' => 'double',
        'bollo' => 'double',
        'xml_file_name' => null,
        'riferimento_amministrazione' => null,
        'banca' => null,
        'dati_trasporto' => null,
        'dati_sdi' => null,
        'dati_pa' => null,
        'righe' => null,
        'spese_documento' => null,
        'scadenze' => null,
        'numero' => null,
        'media_file' => null,
        'allegato' => null,
        'bonus_fiscale_causale' => null,
        'bonus_fiscale_importo' => 'double',
        'bonus_fiscale_percentuale' => 'double',
        'progressivo_invio' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'concurrency_token' => false,
        'anagrafica_id' => false,
        'anagrafica' => false,
        'causale_documento_id' => true,
        'causale_documento' => false,
        'agente_id' => true,
        'agente' => false,
        'pagamento_id' => true,
        'pagamento' => false,
        'vettore_id' => true,
        'vettore' => false,
        'cassa_previdenziale_causale_iva_id' => true,
        'modello_id' => true,
        'cassa_previdenziale_causale_iva' => false,
        'destinazione_id' => true,
        'destinazione' => false,
        'valuta_id' => true,
        'valuta' => false,
        'causale_magazzino_id' => true,
        'causale_magazzino' => false,
        'iva1_causale_iva_id' => true,
        'iva1_causale_iva' => false,
        'iva2_causale_iva_id' => true,
        'iva2_causale_iva' => false,
        'iva3_causale_iva_id' => true,
        'iva3_causale_iva' => false,
        'iva4_causale_iva_id' => true,
        'iva4_causale_iva' => false,
        'tipo_documento' => false,
        'documento_fiscale' => false,
        'destinazione_diversa' => false,
        'iva_esclusa' => false,
        'ritenuta_include_cassa_previdenziale' => false,
        'classe_documento' => false,
        'contatto' => true,
        'numero_parte_testo' => true,
        'numero_parte_numerica' => false,
        'data' => false,
        'stato' => false,
        'data_scadenza_preventivo' => true,
        'data_inizio_scadenze' => true,
        'sconto_percentuale' => false,
        'sconto_incondizionato_importo' => false,
        'abbuono_importo' => false,
        'ritenuta_percentuale' => false,
        'enasarco_percentuale' => false,
        'enasarco_importo' => false,
        'quota_ritenuta_su_imponibile' => false,
        'cambio' => false,
        'cassa_previdenziale_nome' => true,
        'cassa_previdenziale_percentuale' => false,
        'note' => true,
        'sconto_pagamento_percentuale' => false,
        'iva1_imponibile' => false,
        'iva2_imponibile' => false,
        'iva3_imponibile' => false,
        'iva4_imponibile' => false,
        'iva1_imponibile_lordo' => false,
        'iva2_imponibile_lordo' => false,
        'iva3_imponibile_lordo' => false,
        'iva4_imponibile_lordo' => false,
        'iva1_imposta' => false,
        'iva2_imposta' => false,
        'iva3_imposta' => false,
        'iva4_imposta' => false,
        'totale_ritenuta' => false,
        'totale_cassa_previdenziale' => false,
        'totale_spese' => false,
        'totale_spese_esenti' => false,
        'totale_spese_non_esenti' => false,
        'totale_merce' => false,
        'totale_merce_scontata' => false,
        'totale_imponibile' => false,
        'totale_imposta' => false,
        'totale_esente' => false,
        'totale_documento' => false,
        'netto_a_pagare' => false,
        'totale_split_payment' => false,
        'bollo' => false,
        'xml_file_name' => true,
        'riferimento_amministrazione' => true,
        'banca' => false,
        'dati_trasporto' => false,
        'dati_sdi' => false,
        'dati_pa' => false,
        'righe' => true,
        'spese_documento' => true,
        'scadenze' => true,
        'numero' => true,
        'media_file' => false,
        'allegato' => true,
        'bonus_fiscale_causale' => true,
        'bonus_fiscale_importo' => false,
        'bonus_fiscale_percentuale' => false,
        'progressivo_invio' => true
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
        'anagrafica_id' => 'anagraficaId',
        'anagrafica' => 'anagrafica',
        'causale_documento_id' => 'causaleDocumentoId',
        'causale_documento' => 'causaleDocumento',
        'agente_id' => 'agenteId',
        'agente' => 'agente',
        'pagamento_id' => 'pagamentoId',
        'pagamento' => 'pagamento',
        'vettore_id' => 'vettoreId',
        'vettore' => 'vettore',
        'cassa_previdenziale_causale_iva_id' => 'cassaPrevidenzialeCausaleIvaId',
        'modello_id' => 'modelloId',
        'cassa_previdenziale_causale_iva' => 'cassaPrevidenzialeCausaleIva',
        'destinazione_id' => 'destinazioneId',
        'destinazione' => 'destinazione',
        'valuta_id' => 'valutaId',
        'valuta' => 'valuta',
        'causale_magazzino_id' => 'causaleMagazzinoId',
        'causale_magazzino' => 'causaleMagazzino',
        'iva1_causale_iva_id' => 'iva1CausaleIvaId',
        'iva1_causale_iva' => 'iva1CausaleIva',
        'iva2_causale_iva_id' => 'iva2CausaleIvaId',
        'iva2_causale_iva' => 'iva2CausaleIva',
        'iva3_causale_iva_id' => 'iva3CausaleIvaId',
        'iva3_causale_iva' => 'iva3CausaleIva',
        'iva4_causale_iva_id' => 'iva4CausaleIvaId',
        'iva4_causale_iva' => 'iva4CausaleIva',
        'tipo_documento' => 'tipoDocumento',
        'documento_fiscale' => 'documentoFiscale',
        'destinazione_diversa' => 'destinazioneDiversa',
        'iva_esclusa' => 'ivaEsclusa',
        'ritenuta_include_cassa_previdenziale' => 'ritenutaIncludeCassaPrevidenziale',
        'classe_documento' => 'classeDocumento',
        'contatto' => 'contatto',
        'numero_parte_testo' => 'numeroParteTesto',
        'numero_parte_numerica' => 'numeroParteNumerica',
        'data' => 'data',
        'stato' => 'stato',
        'data_scadenza_preventivo' => 'dataScadenzaPreventivo',
        'data_inizio_scadenze' => 'dataInizioScadenze',
        'sconto_percentuale' => 'scontoPercentuale',
        'sconto_incondizionato_importo' => 'scontoIncondizionatoImporto',
        'abbuono_importo' => 'abbuonoImporto',
        'ritenuta_percentuale' => 'ritenutaPercentuale',
        'enasarco_percentuale' => 'enasarcoPercentuale',
        'enasarco_importo' => 'enasarcoImporto',
        'quota_ritenuta_su_imponibile' => 'quotaRitenutaSuImponibile',
        'cambio' => 'cambio',
        'cassa_previdenziale_nome' => 'cassaPrevidenzialeNome',
        'cassa_previdenziale_percentuale' => 'cassaPrevidenzialePercentuale',
        'note' => 'note',
        'sconto_pagamento_percentuale' => 'scontoPagamentoPercentuale',
        'iva1_imponibile' => 'iva1Imponibile',
        'iva2_imponibile' => 'iva2Imponibile',
        'iva3_imponibile' => 'iva3Imponibile',
        'iva4_imponibile' => 'iva4Imponibile',
        'iva1_imponibile_lordo' => 'iva1ImponibileLordo',
        'iva2_imponibile_lordo' => 'iva2ImponibileLordo',
        'iva3_imponibile_lordo' => 'iva3ImponibileLordo',
        'iva4_imponibile_lordo' => 'iva4ImponibileLordo',
        'iva1_imposta' => 'iva1Imposta',
        'iva2_imposta' => 'iva2Imposta',
        'iva3_imposta' => 'iva3Imposta',
        'iva4_imposta' => 'iva4Imposta',
        'totale_ritenuta' => 'totaleRitenuta',
        'totale_cassa_previdenziale' => 'totaleCassaPrevidenziale',
        'totale_spese' => 'totaleSpese',
        'totale_spese_esenti' => 'totaleSpeseEsenti',
        'totale_spese_non_esenti' => 'totaleSpeseNonEsenti',
        'totale_merce' => 'totaleMerce',
        'totale_merce_scontata' => 'totaleMerceScontata',
        'totale_imponibile' => 'totaleImponibile',
        'totale_imposta' => 'totaleImposta',
        'totale_esente' => 'totaleEsente',
        'totale_documento' => 'totaleDocumento',
        'netto_a_pagare' => 'nettoAPagare',
        'totale_split_payment' => 'totaleSplitPayment',
        'bollo' => 'bollo',
        'xml_file_name' => 'xmlFileName',
        'riferimento_amministrazione' => 'riferimentoAmministrazione',
        'banca' => 'banca',
        'dati_trasporto' => 'datiTrasporto',
        'dati_sdi' => 'datiSdi',
        'dati_pa' => 'datiPA',
        'righe' => 'righe',
        'spese_documento' => 'speseDocumento',
        'scadenze' => 'scadenze',
        'numero' => 'numero',
        'media_file' => 'mediaFile',
        'allegato' => 'allegato',
        'bonus_fiscale_causale' => 'bonusFiscaleCausale',
        'bonus_fiscale_importo' => 'bonusFiscaleImporto',
        'bonus_fiscale_percentuale' => 'bonusFiscalePercentuale',
        'progressivo_invio' => 'progressivoInvio'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'concurrency_token' => 'setConcurrencyToken',
        'anagrafica_id' => 'setAnagraficaId',
        'anagrafica' => 'setAnagrafica',
        'causale_documento_id' => 'setCausaleDocumentoId',
        'causale_documento' => 'setCausaleDocumento',
        'agente_id' => 'setAgenteId',
        'agente' => 'setAgente',
        'pagamento_id' => 'setPagamentoId',
        'pagamento' => 'setPagamento',
        'vettore_id' => 'setVettoreId',
        'vettore' => 'setVettore',
        'cassa_previdenziale_causale_iva_id' => 'setCassaPrevidenzialeCausaleIvaId',
        'modello_id' => 'setModelloId',
        'cassa_previdenziale_causale_iva' => 'setCassaPrevidenzialeCausaleIva',
        'destinazione_id' => 'setDestinazioneId',
        'destinazione' => 'setDestinazione',
        'valuta_id' => 'setValutaId',
        'valuta' => 'setValuta',
        'causale_magazzino_id' => 'setCausaleMagazzinoId',
        'causale_magazzino' => 'setCausaleMagazzino',
        'iva1_causale_iva_id' => 'setIva1CausaleIvaId',
        'iva1_causale_iva' => 'setIva1CausaleIva',
        'iva2_causale_iva_id' => 'setIva2CausaleIvaId',
        'iva2_causale_iva' => 'setIva2CausaleIva',
        'iva3_causale_iva_id' => 'setIva3CausaleIvaId',
        'iva3_causale_iva' => 'setIva3CausaleIva',
        'iva4_causale_iva_id' => 'setIva4CausaleIvaId',
        'iva4_causale_iva' => 'setIva4CausaleIva',
        'tipo_documento' => 'setTipoDocumento',
        'documento_fiscale' => 'setDocumentoFiscale',
        'destinazione_diversa' => 'setDestinazioneDiversa',
        'iva_esclusa' => 'setIvaEsclusa',
        'ritenuta_include_cassa_previdenziale' => 'setRitenutaIncludeCassaPrevidenziale',
        'classe_documento' => 'setClasseDocumento',
        'contatto' => 'setContatto',
        'numero_parte_testo' => 'setNumeroParteTesto',
        'numero_parte_numerica' => 'setNumeroParteNumerica',
        'data' => 'setData',
        'stato' => 'setStato',
        'data_scadenza_preventivo' => 'setDataScadenzaPreventivo',
        'data_inizio_scadenze' => 'setDataInizioScadenze',
        'sconto_percentuale' => 'setScontoPercentuale',
        'sconto_incondizionato_importo' => 'setScontoIncondizionatoImporto',
        'abbuono_importo' => 'setAbbuonoImporto',
        'ritenuta_percentuale' => 'setRitenutaPercentuale',
        'enasarco_percentuale' => 'setEnasarcoPercentuale',
        'enasarco_importo' => 'setEnasarcoImporto',
        'quota_ritenuta_su_imponibile' => 'setQuotaRitenutaSuImponibile',
        'cambio' => 'setCambio',
        'cassa_previdenziale_nome' => 'setCassaPrevidenzialeNome',
        'cassa_previdenziale_percentuale' => 'setCassaPrevidenzialePercentuale',
        'note' => 'setNote',
        'sconto_pagamento_percentuale' => 'setScontoPagamentoPercentuale',
        'iva1_imponibile' => 'setIva1Imponibile',
        'iva2_imponibile' => 'setIva2Imponibile',
        'iva3_imponibile' => 'setIva3Imponibile',
        'iva4_imponibile' => 'setIva4Imponibile',
        'iva1_imponibile_lordo' => 'setIva1ImponibileLordo',
        'iva2_imponibile_lordo' => 'setIva2ImponibileLordo',
        'iva3_imponibile_lordo' => 'setIva3ImponibileLordo',
        'iva4_imponibile_lordo' => 'setIva4ImponibileLordo',
        'iva1_imposta' => 'setIva1Imposta',
        'iva2_imposta' => 'setIva2Imposta',
        'iva3_imposta' => 'setIva3Imposta',
        'iva4_imposta' => 'setIva4Imposta',
        'totale_ritenuta' => 'setTotaleRitenuta',
        'totale_cassa_previdenziale' => 'setTotaleCassaPrevidenziale',
        'totale_spese' => 'setTotaleSpese',
        'totale_spese_esenti' => 'setTotaleSpeseEsenti',
        'totale_spese_non_esenti' => 'setTotaleSpeseNonEsenti',
        'totale_merce' => 'setTotaleMerce',
        'totale_merce_scontata' => 'setTotaleMerceScontata',
        'totale_imponibile' => 'setTotaleImponibile',
        'totale_imposta' => 'setTotaleImposta',
        'totale_esente' => 'setTotaleEsente',
        'totale_documento' => 'setTotaleDocumento',
        'netto_a_pagare' => 'setNettoAPagare',
        'totale_split_payment' => 'setTotaleSplitPayment',
        'bollo' => 'setBollo',
        'xml_file_name' => 'setXmlFileName',
        'riferimento_amministrazione' => 'setRiferimentoAmministrazione',
        'banca' => 'setBanca',
        'dati_trasporto' => 'setDatiTrasporto',
        'dati_sdi' => 'setDatiSdi',
        'dati_pa' => 'setDatiPa',
        'righe' => 'setRighe',
        'spese_documento' => 'setSpeseDocumento',
        'scadenze' => 'setScadenze',
        'numero' => 'setNumero',
        'media_file' => 'setMediaFile',
        'allegato' => 'setAllegato',
        'bonus_fiscale_causale' => 'setBonusFiscaleCausale',
        'bonus_fiscale_importo' => 'setBonusFiscaleImporto',
        'bonus_fiscale_percentuale' => 'setBonusFiscalePercentuale',
        'progressivo_invio' => 'setProgressivoInvio'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'concurrency_token' => 'getConcurrencyToken',
        'anagrafica_id' => 'getAnagraficaId',
        'anagrafica' => 'getAnagrafica',
        'causale_documento_id' => 'getCausaleDocumentoId',
        'causale_documento' => 'getCausaleDocumento',
        'agente_id' => 'getAgenteId',
        'agente' => 'getAgente',
        'pagamento_id' => 'getPagamentoId',
        'pagamento' => 'getPagamento',
        'vettore_id' => 'getVettoreId',
        'vettore' => 'getVettore',
        'cassa_previdenziale_causale_iva_id' => 'getCassaPrevidenzialeCausaleIvaId',
        'modello_id' => 'getModelloId',
        'cassa_previdenziale_causale_iva' => 'getCassaPrevidenzialeCausaleIva',
        'destinazione_id' => 'getDestinazioneId',
        'destinazione' => 'getDestinazione',
        'valuta_id' => 'getValutaId',
        'valuta' => 'getValuta',
        'causale_magazzino_id' => 'getCausaleMagazzinoId',
        'causale_magazzino' => 'getCausaleMagazzino',
        'iva1_causale_iva_id' => 'getIva1CausaleIvaId',
        'iva1_causale_iva' => 'getIva1CausaleIva',
        'iva2_causale_iva_id' => 'getIva2CausaleIvaId',
        'iva2_causale_iva' => 'getIva2CausaleIva',
        'iva3_causale_iva_id' => 'getIva3CausaleIvaId',
        'iva3_causale_iva' => 'getIva3CausaleIva',
        'iva4_causale_iva_id' => 'getIva4CausaleIvaId',
        'iva4_causale_iva' => 'getIva4CausaleIva',
        'tipo_documento' => 'getTipoDocumento',
        'documento_fiscale' => 'getDocumentoFiscale',
        'destinazione_diversa' => 'getDestinazioneDiversa',
        'iva_esclusa' => 'getIvaEsclusa',
        'ritenuta_include_cassa_previdenziale' => 'getRitenutaIncludeCassaPrevidenziale',
        'classe_documento' => 'getClasseDocumento',
        'contatto' => 'getContatto',
        'numero_parte_testo' => 'getNumeroParteTesto',
        'numero_parte_numerica' => 'getNumeroParteNumerica',
        'data' => 'getData',
        'stato' => 'getStato',
        'data_scadenza_preventivo' => 'getDataScadenzaPreventivo',
        'data_inizio_scadenze' => 'getDataInizioScadenze',
        'sconto_percentuale' => 'getScontoPercentuale',
        'sconto_incondizionato_importo' => 'getScontoIncondizionatoImporto',
        'abbuono_importo' => 'getAbbuonoImporto',
        'ritenuta_percentuale' => 'getRitenutaPercentuale',
        'enasarco_percentuale' => 'getEnasarcoPercentuale',
        'enasarco_importo' => 'getEnasarcoImporto',
        'quota_ritenuta_su_imponibile' => 'getQuotaRitenutaSuImponibile',
        'cambio' => 'getCambio',
        'cassa_previdenziale_nome' => 'getCassaPrevidenzialeNome',
        'cassa_previdenziale_percentuale' => 'getCassaPrevidenzialePercentuale',
        'note' => 'getNote',
        'sconto_pagamento_percentuale' => 'getScontoPagamentoPercentuale',
        'iva1_imponibile' => 'getIva1Imponibile',
        'iva2_imponibile' => 'getIva2Imponibile',
        'iva3_imponibile' => 'getIva3Imponibile',
        'iva4_imponibile' => 'getIva4Imponibile',
        'iva1_imponibile_lordo' => 'getIva1ImponibileLordo',
        'iva2_imponibile_lordo' => 'getIva2ImponibileLordo',
        'iva3_imponibile_lordo' => 'getIva3ImponibileLordo',
        'iva4_imponibile_lordo' => 'getIva4ImponibileLordo',
        'iva1_imposta' => 'getIva1Imposta',
        'iva2_imposta' => 'getIva2Imposta',
        'iva3_imposta' => 'getIva3Imposta',
        'iva4_imposta' => 'getIva4Imposta',
        'totale_ritenuta' => 'getTotaleRitenuta',
        'totale_cassa_previdenziale' => 'getTotaleCassaPrevidenziale',
        'totale_spese' => 'getTotaleSpese',
        'totale_spese_esenti' => 'getTotaleSpeseEsenti',
        'totale_spese_non_esenti' => 'getTotaleSpeseNonEsenti',
        'totale_merce' => 'getTotaleMerce',
        'totale_merce_scontata' => 'getTotaleMerceScontata',
        'totale_imponibile' => 'getTotaleImponibile',
        'totale_imposta' => 'getTotaleImposta',
        'totale_esente' => 'getTotaleEsente',
        'totale_documento' => 'getTotaleDocumento',
        'netto_a_pagare' => 'getNettoAPagare',
        'totale_split_payment' => 'getTotaleSplitPayment',
        'bollo' => 'getBollo',
        'xml_file_name' => 'getXmlFileName',
        'riferimento_amministrazione' => 'getRiferimentoAmministrazione',
        'banca' => 'getBanca',
        'dati_trasporto' => 'getDatiTrasporto',
        'dati_sdi' => 'getDatiSdi',
        'dati_pa' => 'getDatiPa',
        'righe' => 'getRighe',
        'spese_documento' => 'getSpeseDocumento',
        'scadenze' => 'getScadenze',
        'numero' => 'getNumero',
        'media_file' => 'getMediaFile',
        'allegato' => 'getAllegato',
        'bonus_fiscale_causale' => 'getBonusFiscaleCausale',
        'bonus_fiscale_importo' => 'getBonusFiscaleImporto',
        'bonus_fiscale_percentuale' => 'getBonusFiscalePercentuale',
        'progressivo_invio' => 'getProgressivoInvio'
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
    public const DOCUMENTO_FISCALE_VENDITA = 'Vendita';
    public const DOCUMENTO_FISCALE_ACQUISTO = 'Acquisto';
    public const DOCUMENTO_FISCALE_NON_FISCALE = 'NonFiscale';
    public const CLASSE_DOCUMENTO_VENDITA = 'Vendita';
    public const CLASSE_DOCUMENTO_ACQUISTO = 'Acquisto';
    public const STATO_BOZZA = 'Bozza';
    public const STATO_FATTURATO = 'Fatturato';
    public const STATO_EMESSO = 'Emesso';
    public const STATO_FATTURA_ACCONTO_SCONTATA_IN_FATTURA = 'FatturaAccontoScontataInFattura';
    public const STATO_PREVENTIVO_CHIUSO = 'PreventivoChiuso';
    public const STATO_ORDINE_EVASO = 'OrdineEvaso';
    public const STATO_ORDINE_PARZIALMENTE_EVASO = 'OrdineParzialmenteEvaso';
    public const STATO_ORDINE_ANNULLATO = 'OrdineAnnullato';
    public const STATO_RESO_ACCREDITATO = 'ResoAccreditato';
    public const STATO_DDT_NON_FATTURARE = 'DdtNonFatturare';

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
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getDocumentoFiscaleAllowableValues()
    {
        return [
            self::DOCUMENTO_FISCALE_VENDITA,
            self::DOCUMENTO_FISCALE_ACQUISTO,
            self::DOCUMENTO_FISCALE_NON_FISCALE,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getClasseDocumentoAllowableValues()
    {
        return [
            self::CLASSE_DOCUMENTO_VENDITA,
            self::CLASSE_DOCUMENTO_ACQUISTO,
        ];
    }

    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatoAllowableValues()
    {
        return [
            self::STATO_BOZZA,
            self::STATO_FATTURATO,
            self::STATO_EMESSO,
            self::STATO_FATTURA_ACCONTO_SCONTATA_IN_FATTURA,
            self::STATO_PREVENTIVO_CHIUSO,
            self::STATO_ORDINE_EVASO,
            self::STATO_ORDINE_PARZIALMENTE_EVASO,
            self::STATO_ORDINE_ANNULLATO,
            self::STATO_RESO_ACCREDITATO,
            self::STATO_DDT_NON_FATTURARE,
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
        $this->setIfExists('anagrafica_id', $data ?? [], null);
        $this->setIfExists('anagrafica', $data ?? [], null);
        $this->setIfExists('causale_documento_id', $data ?? [], null);
        $this->setIfExists('causale_documento', $data ?? [], null);
        $this->setIfExists('agente_id', $data ?? [], null);
        $this->setIfExists('agente', $data ?? [], null);
        $this->setIfExists('pagamento_id', $data ?? [], null);
        $this->setIfExists('pagamento', $data ?? [], null);
        $this->setIfExists('vettore_id', $data ?? [], null);
        $this->setIfExists('vettore', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_causale_iva_id', $data ?? [], null);
        $this->setIfExists('modello_id', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_causale_iva', $data ?? [], null);
        $this->setIfExists('destinazione_id', $data ?? [], null);
        $this->setIfExists('destinazione', $data ?? [], null);
        $this->setIfExists('valuta_id', $data ?? [], null);
        $this->setIfExists('valuta', $data ?? [], null);
        $this->setIfExists('causale_magazzino_id', $data ?? [], null);
        $this->setIfExists('causale_magazzino', $data ?? [], null);
        $this->setIfExists('iva1_causale_iva_id', $data ?? [], null);
        $this->setIfExists('iva1_causale_iva', $data ?? [], null);
        $this->setIfExists('iva2_causale_iva_id', $data ?? [], null);
        $this->setIfExists('iva2_causale_iva', $data ?? [], null);
        $this->setIfExists('iva3_causale_iva_id', $data ?? [], null);
        $this->setIfExists('iva3_causale_iva', $data ?? [], null);
        $this->setIfExists('iva4_causale_iva_id', $data ?? [], null);
        $this->setIfExists('iva4_causale_iva', $data ?? [], null);
        $this->setIfExists('tipo_documento', $data ?? [], null);
        $this->setIfExists('documento_fiscale', $data ?? [], null);
        $this->setIfExists('destinazione_diversa', $data ?? [], null);
        $this->setIfExists('iva_esclusa', $data ?? [], null);
        $this->setIfExists('ritenuta_include_cassa_previdenziale', $data ?? [], null);
        $this->setIfExists('classe_documento', $data ?? [], null);
        $this->setIfExists('contatto', $data ?? [], null);
        $this->setIfExists('numero_parte_testo', $data ?? [], null);
        $this->setIfExists('numero_parte_numerica', $data ?? [], null);
        $this->setIfExists('data', $data ?? [], null);
        $this->setIfExists('stato', $data ?? [], null);
        $this->setIfExists('data_scadenza_preventivo', $data ?? [], null);
        $this->setIfExists('data_inizio_scadenze', $data ?? [], null);
        $this->setIfExists('sconto_percentuale', $data ?? [], null);
        $this->setIfExists('sconto_incondizionato_importo', $data ?? [], null);
        $this->setIfExists('abbuono_importo', $data ?? [], null);
        $this->setIfExists('ritenuta_percentuale', $data ?? [], null);
        $this->setIfExists('enasarco_percentuale', $data ?? [], null);
        $this->setIfExists('enasarco_importo', $data ?? [], null);
        $this->setIfExists('quota_ritenuta_su_imponibile', $data ?? [], null);
        $this->setIfExists('cambio', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_nome', $data ?? [], null);
        $this->setIfExists('cassa_previdenziale_percentuale', $data ?? [], null);
        $this->setIfExists('note', $data ?? [], null);
        $this->setIfExists('sconto_pagamento_percentuale', $data ?? [], null);
        $this->setIfExists('iva1_imponibile', $data ?? [], null);
        $this->setIfExists('iva2_imponibile', $data ?? [], null);
        $this->setIfExists('iva3_imponibile', $data ?? [], null);
        $this->setIfExists('iva4_imponibile', $data ?? [], null);
        $this->setIfExists('iva1_imponibile_lordo', $data ?? [], null);
        $this->setIfExists('iva2_imponibile_lordo', $data ?? [], null);
        $this->setIfExists('iva3_imponibile_lordo', $data ?? [], null);
        $this->setIfExists('iva4_imponibile_lordo', $data ?? [], null);
        $this->setIfExists('iva1_imposta', $data ?? [], null);
        $this->setIfExists('iva2_imposta', $data ?? [], null);
        $this->setIfExists('iva3_imposta', $data ?? [], null);
        $this->setIfExists('iva4_imposta', $data ?? [], null);
        $this->setIfExists('totale_ritenuta', $data ?? [], null);
        $this->setIfExists('totale_cassa_previdenziale', $data ?? [], null);
        $this->setIfExists('totale_spese', $data ?? [], null);
        $this->setIfExists('totale_spese_esenti', $data ?? [], null);
        $this->setIfExists('totale_spese_non_esenti', $data ?? [], null);
        $this->setIfExists('totale_merce', $data ?? [], null);
        $this->setIfExists('totale_merce_scontata', $data ?? [], null);
        $this->setIfExists('totale_imponibile', $data ?? [], null);
        $this->setIfExists('totale_imposta', $data ?? [], null);
        $this->setIfExists('totale_esente', $data ?? [], null);
        $this->setIfExists('totale_documento', $data ?? [], null);
        $this->setIfExists('netto_a_pagare', $data ?? [], null);
        $this->setIfExists('totale_split_payment', $data ?? [], null);
        $this->setIfExists('bollo', $data ?? [], null);
        $this->setIfExists('xml_file_name', $data ?? [], null);
        $this->setIfExists('riferimento_amministrazione', $data ?? [], null);
        $this->setIfExists('banca', $data ?? [], null);
        $this->setIfExists('dati_trasporto', $data ?? [], null);
        $this->setIfExists('dati_sdi', $data ?? [], null);
        $this->setIfExists('dati_pa', $data ?? [], null);
        $this->setIfExists('righe', $data ?? [], null);
        $this->setIfExists('spese_documento', $data ?? [], null);
        $this->setIfExists('scadenze', $data ?? [], null);
        $this->setIfExists('numero', $data ?? [], null);
        $this->setIfExists('media_file', $data ?? [], null);
        $this->setIfExists('allegato', $data ?? [], null);
        $this->setIfExists('bonus_fiscale_causale', $data ?? [], null);
        $this->setIfExists('bonus_fiscale_importo', $data ?? [], null);
        $this->setIfExists('bonus_fiscale_percentuale', $data ?? [], null);
        $this->setIfExists('progressivo_invio', $data ?? [], null);
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

        $allowedValues = $this->getTipoDocumentoAllowableValues();
        if (!is_null($this->container['tipo_documento']) && !in_array($this->container['tipo_documento'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'tipo_documento', must be one of '%s'",
                $this->container['tipo_documento'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getDocumentoFiscaleAllowableValues();
        if (!is_null($this->container['documento_fiscale']) && !in_array($this->container['documento_fiscale'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'documento_fiscale', must be one of '%s'",
                $this->container['documento_fiscale'],
                implode("', '", $allowedValues)
            );
        }

        $allowedValues = $this->getClasseDocumentoAllowableValues();
        if (!is_null($this->container['classe_documento']) && !in_array($this->container['classe_documento'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'classe_documento', must be one of '%s'",
                $this->container['classe_documento'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['numero_parte_testo']) && (mb_strlen($this->container['numero_parte_testo']) > 40)) {
            $invalidProperties[] = "invalid value for 'numero_parte_testo', the character length must be smaller than or equal to 40.";
        }

        if (!is_null($this->container['numero_parte_testo']) && (mb_strlen($this->container['numero_parte_testo']) < 0)) {
            $invalidProperties[] = "invalid value for 'numero_parte_testo', the character length must be bigger than or equal to 0.";
        }

        if (!is_null($this->container['numero_parte_numerica']) && ($this->container['numero_parte_numerica'] > 2147483647)) {
            $invalidProperties[] = "invalid value for 'numero_parte_numerica', must be smaller than or equal to 2147483647.";
        }

        if (!is_null($this->container['numero_parte_numerica']) && ($this->container['numero_parte_numerica'] < 0)) {
            $invalidProperties[] = "invalid value for 'numero_parte_numerica', must be bigger than or equal to 0.";
        }

        $allowedValues = $this->getStatoAllowableValues();
        if (!is_null($this->container['stato']) && !in_array($this->container['stato'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'stato', must be one of '%s'",
                $this->container['stato'],
                implode("', '", $allowedValues)
            );
        }

        if (!is_null($this->container['cassa_previdenziale_nome']) && (mb_strlen($this->container['cassa_previdenziale_nome']) > 50)) {
            $invalidProperties[] = "invalid value for 'cassa_previdenziale_nome', the character length must be smaller than or equal to 50.";
        }

        if (!is_null($this->container['cassa_previdenziale_nome']) && (mb_strlen($this->container['cassa_previdenziale_nome']) < 0)) {
            $invalidProperties[] = "invalid value for 'cassa_previdenziale_nome', the character length must be bigger than or equal to 0.";
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
            throw new \InvalidArgumentException('non-nullable anagrafica_id cannot be null');
        }
        $this->container['anagrafica_id'] = $anagrafica_id;

        return $this;
    }

    /**
     * Gets anagrafica
     *
     * @return \Amica\Sdk\Model\Anagrafica|null
     */
    public function getAnagrafica()
    {
        return $this->container['anagrafica'];
    }

    /**
     * Sets anagrafica
     *
     * @param \Amica\Sdk\Model\Anagrafica|null $anagrafica anagrafica
     *
     * @return self
     */
    public function setAnagrafica($anagrafica)
    {
        if (is_null($anagrafica)) {
            throw new \InvalidArgumentException('non-nullable anagrafica cannot be null');
        }
        $this->container['anagrafica'] = $anagrafica;

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
     * @param int|null $causale_documento_id Identificativo univoco di `CausaleDocumento`.
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
     * Gets causale_documento
     *
     * @return \Amica\Sdk\Model\CausaleDocumento|null
     */
    public function getCausaleDocumento()
    {
        return $this->container['causale_documento'];
    }

    /**
     * Sets causale_documento
     *
     * @param \Amica\Sdk\Model\CausaleDocumento|null $causale_documento causale_documento
     *
     * @return self
     */
    public function setCausaleDocumento($causale_documento)
    {
        if (is_null($causale_documento)) {
            throw new \InvalidArgumentException('non-nullable causale_documento cannot be null');
        }
        $this->container['causale_documento'] = $causale_documento;

        return $this;
    }

    /**
     * Gets agente_id
     *
     * @return int|null
     */
    public function getAgenteId()
    {
        return $this->container['agente_id'];
    }

    /**
     * Sets agente_id
     *
     * @param int|null $agente_id Identificativo univoco di `Anagrafica` dell'agente.
     *
     * @return self
     */
    public function setAgenteId($agente_id)
    {
        if (is_null($agente_id)) {
            array_push($this->openAPINullablesSetToNull, 'agente_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('agente_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['agente_id'] = $agente_id;

        return $this;
    }

    /**
     * Gets agente
     *
     * @return \Amica\Sdk\Model\Anagrafica|null
     */
    public function getAgente()
    {
        return $this->container['agente'];
    }

    /**
     * Sets agente
     *
     * @param \Amica\Sdk\Model\Anagrafica|null $agente agente
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
     * Gets pagamento
     *
     * @return \Amica\Sdk\Model\Pagamento|null
     */
    public function getPagamento()
    {
        return $this->container['pagamento'];
    }

    /**
     * Sets pagamento
     *
     * @param \Amica\Sdk\Model\Pagamento|null $pagamento pagamento
     *
     * @return self
     */
    public function setPagamento($pagamento)
    {
        if (is_null($pagamento)) {
            throw new \InvalidArgumentException('non-nullable pagamento cannot be null');
        }
        $this->container['pagamento'] = $pagamento;

        return $this;
    }

    /**
     * Gets vettore_id
     *
     * @return int|null
     */
    public function getVettoreId()
    {
        return $this->container['vettore_id'];
    }

    /**
     * Sets vettore_id
     *
     * @param int|null $vettore_id Identificativo univoco di `Anagrafica` del vettore.
     *
     * @return self
     */
    public function setVettoreId($vettore_id)
    {
        if (is_null($vettore_id)) {
            array_push($this->openAPINullablesSetToNull, 'vettore_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('vettore_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['vettore_id'] = $vettore_id;

        return $this;
    }

    /**
     * Gets vettore
     *
     * @return \Amica\Sdk\Model\Anagrafica|null
     */
    public function getVettore()
    {
        return $this->container['vettore'];
    }

    /**
     * Sets vettore
     *
     * @param \Amica\Sdk\Model\Anagrafica|null $vettore vettore
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
     * Gets cassa_previdenziale_causale_iva_id
     *
     * @return int|null
     */
    public function getCassaPrevidenzialeCausaleIvaId()
    {
        return $this->container['cassa_previdenziale_causale_iva_id'];
    }

    /**
     * Sets cassa_previdenziale_causale_iva_id
     *
     * @param int|null $cassa_previdenziale_causale_iva_id Identificativo univoco della causale IVA della cassa previdenziale.
     *
     * @return self
     */
    public function setCassaPrevidenzialeCausaleIvaId($cassa_previdenziale_causale_iva_id)
    {
        if (is_null($cassa_previdenziale_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'cassa_previdenziale_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cassa_previdenziale_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cassa_previdenziale_causale_iva_id'] = $cassa_previdenziale_causale_iva_id;

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
     * @param int|null $modello_id Obsoleto. Non utilizzare.
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
     * Gets cassa_previdenziale_causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getCassaPrevidenzialeCausaleIva()
    {
        return $this->container['cassa_previdenziale_causale_iva'];
    }

    /**
     * Sets cassa_previdenziale_causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $cassa_previdenziale_causale_iva cassa_previdenziale_causale_iva
     *
     * @return self
     */
    public function setCassaPrevidenzialeCausaleIva($cassa_previdenziale_causale_iva)
    {
        if (is_null($cassa_previdenziale_causale_iva)) {
            throw new \InvalidArgumentException('non-nullable cassa_previdenziale_causale_iva cannot be null');
        }
        $this->container['cassa_previdenziale_causale_iva'] = $cassa_previdenziale_causale_iva;

        return $this;
    }

    /**
     * Gets destinazione_id
     *
     * @return int|null
     */
    public function getDestinazioneId()
    {
        return $this->container['destinazione_id'];
    }

    /**
     * Sets destinazione_id
     *
     * @param int|null $destinazione_id Identificativo univoco di `Indirizzo` di destinazione.
     *
     * @return self
     */
    public function setDestinazioneId($destinazione_id)
    {
        if (is_null($destinazione_id)) {
            array_push($this->openAPINullablesSetToNull, 'destinazione_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('destinazione_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['destinazione_id'] = $destinazione_id;

        return $this;
    }

    /**
     * Gets destinazione
     *
     * @return \Amica\Sdk\Model\Indirizzo|null
     */
    public function getDestinazione()
    {
        return $this->container['destinazione'];
    }

    /**
     * Sets destinazione
     *
     * @param \Amica\Sdk\Model\Indirizzo|null $destinazione destinazione
     *
     * @return self
     */
    public function setDestinazione($destinazione)
    {
        if (is_null($destinazione)) {
            throw new \InvalidArgumentException('non-nullable destinazione cannot be null');
        }
        $this->container['destinazione'] = $destinazione;

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
     * @param int|null $valuta_id Identificativo univoco di `Valuta`.
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
     * Gets causale_magazzino_id
     *
     * @return int|null
     */
    public function getCausaleMagazzinoId()
    {
        return $this->container['causale_magazzino_id'];
    }

    /**
     * Sets causale_magazzino_id
     *
     * @param int|null $causale_magazzino_id Identificativo univoco di `CausaleMagazzino`.
     *
     * @return self
     */
    public function setCausaleMagazzinoId($causale_magazzino_id)
    {
        if (is_null($causale_magazzino_id)) {
            array_push($this->openAPINullablesSetToNull, 'causale_magazzino_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('causale_magazzino_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['causale_magazzino_id'] = $causale_magazzino_id;

        return $this;
    }

    /**
     * Gets causale_magazzino
     *
     * @return \Amica\Sdk\Model\CausaleMagazzino|null
     */
    public function getCausaleMagazzino()
    {
        return $this->container['causale_magazzino'];
    }

    /**
     * Sets causale_magazzino
     *
     * @param \Amica\Sdk\Model\CausaleMagazzino|null $causale_magazzino causale_magazzino
     *
     * @return self
     */
    public function setCausaleMagazzino($causale_magazzino)
    {
        if (is_null($causale_magazzino)) {
            throw new \InvalidArgumentException('non-nullable causale_magazzino cannot be null');
        }
        $this->container['causale_magazzino'] = $causale_magazzino;

        return $this;
    }

    /**
     * Gets iva1_causale_iva_id
     *
     * @return int|null
     */
    public function getIva1CausaleIvaId()
    {
        return $this->container['iva1_causale_iva_id'];
    }

    /**
     * Sets iva1_causale_iva_id
     *
     * @param int|null $iva1_causale_iva_id Identificativo univoco della prima causale IVA.
     *
     * @return self
     */
    public function setIva1CausaleIvaId($iva1_causale_iva_id)
    {
        if (is_null($iva1_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'iva1_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('iva1_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['iva1_causale_iva_id'] = $iva1_causale_iva_id;

        return $this;
    }

    /**
     * Gets iva1_causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getIva1CausaleIva()
    {
        return $this->container['iva1_causale_iva'];
    }

    /**
     * Sets iva1_causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $iva1_causale_iva iva1_causale_iva
     *
     * @return self
     */
    public function setIva1CausaleIva($iva1_causale_iva)
    {
        if (is_null($iva1_causale_iva)) {
            throw new \InvalidArgumentException('non-nullable iva1_causale_iva cannot be null');
        }
        $this->container['iva1_causale_iva'] = $iva1_causale_iva;

        return $this;
    }

    /**
     * Gets iva2_causale_iva_id
     *
     * @return int|null
     */
    public function getIva2CausaleIvaId()
    {
        return $this->container['iva2_causale_iva_id'];
    }

    /**
     * Sets iva2_causale_iva_id
     *
     * @param int|null $iva2_causale_iva_id Identificativo univoco della seconda causale IVA.
     *
     * @return self
     */
    public function setIva2CausaleIvaId($iva2_causale_iva_id)
    {
        if (is_null($iva2_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'iva2_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('iva2_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['iva2_causale_iva_id'] = $iva2_causale_iva_id;

        return $this;
    }

    /**
     * Gets iva2_causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getIva2CausaleIva()
    {
        return $this->container['iva2_causale_iva'];
    }

    /**
     * Sets iva2_causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $iva2_causale_iva iva2_causale_iva
     *
     * @return self
     */
    public function setIva2CausaleIva($iva2_causale_iva)
    {
        if (is_null($iva2_causale_iva)) {
            throw new \InvalidArgumentException('non-nullable iva2_causale_iva cannot be null');
        }
        $this->container['iva2_causale_iva'] = $iva2_causale_iva;

        return $this;
    }

    /**
     * Gets iva3_causale_iva_id
     *
     * @return int|null
     */
    public function getIva3CausaleIvaId()
    {
        return $this->container['iva3_causale_iva_id'];
    }

    /**
     * Sets iva3_causale_iva_id
     *
     * @param int|null $iva3_causale_iva_id Identificativo univoco della terza causale IVA.
     *
     * @return self
     */
    public function setIva3CausaleIvaId($iva3_causale_iva_id)
    {
        if (is_null($iva3_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'iva3_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('iva3_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['iva3_causale_iva_id'] = $iva3_causale_iva_id;

        return $this;
    }

    /**
     * Gets iva3_causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getIva3CausaleIva()
    {
        return $this->container['iva3_causale_iva'];
    }

    /**
     * Sets iva3_causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $iva3_causale_iva iva3_causale_iva
     *
     * @return self
     */
    public function setIva3CausaleIva($iva3_causale_iva)
    {
        if (is_null($iva3_causale_iva)) {
            throw new \InvalidArgumentException('non-nullable iva3_causale_iva cannot be null');
        }
        $this->container['iva3_causale_iva'] = $iva3_causale_iva;

        return $this;
    }

    /**
     * Gets iva4_causale_iva_id
     *
     * @return int|null
     */
    public function getIva4CausaleIvaId()
    {
        return $this->container['iva4_causale_iva_id'];
    }

    /**
     * Sets iva4_causale_iva_id
     *
     * @param int|null $iva4_causale_iva_id Identificativo univoco della quarta causale IVA.
     *
     * @return self
     */
    public function setIva4CausaleIvaId($iva4_causale_iva_id)
    {
        if (is_null($iva4_causale_iva_id)) {
            array_push($this->openAPINullablesSetToNull, 'iva4_causale_iva_id');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('iva4_causale_iva_id', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['iva4_causale_iva_id'] = $iva4_causale_iva_id;

        return $this;
    }

    /**
     * Gets iva4_causale_iva
     *
     * @return \Amica\Sdk\Model\CausaleIva|null
     */
    public function getIva4CausaleIva()
    {
        return $this->container['iva4_causale_iva'];
    }

    /**
     * Sets iva4_causale_iva
     *
     * @param \Amica\Sdk\Model\CausaleIva|null $iva4_causale_iva iva4_causale_iva
     *
     * @return self
     */
    public function setIva4CausaleIva($iva4_causale_iva)
    {
        if (is_null($iva4_causale_iva)) {
            throw new \InvalidArgumentException('non-nullable iva4_causale_iva cannot be null');
        }
        $this->container['iva4_causale_iva'] = $iva4_causale_iva;

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
     * @param string|null $tipo_documento Tipologia del documento.
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
     * Gets documento_fiscale
     *
     * @return string|null
     */
    public function getDocumentoFiscale()
    {
        return $this->container['documento_fiscale'];
    }

    /**
     * Sets documento_fiscale
     *
     * @param string|null $documento_fiscale Tipo di documento fiscale.
     *
     * @return self
     */
    public function setDocumentoFiscale($documento_fiscale)
    {
        if (is_null($documento_fiscale)) {
            throw new \InvalidArgumentException('non-nullable documento_fiscale cannot be null');
        }
        $allowedValues = $this->getDocumentoFiscaleAllowableValues();
        if (!in_array($documento_fiscale, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'documento_fiscale', must be one of '%s'",
                    $documento_fiscale,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['documento_fiscale'] = $documento_fiscale;

        return $this;
    }

    /**
     * Gets destinazione_diversa
     *
     * @return bool|null
     */
    public function getDestinazioneDiversa()
    {
        return $this->container['destinazione_diversa'];
    }

    /**
     * Sets destinazione_diversa
     *
     * @param bool|null $destinazione_diversa Indica se la destinazione della merce è diversa dall'indirizzo principale.
     *
     * @return self
     */
    public function setDestinazioneDiversa($destinazione_diversa)
    {
        if (is_null($destinazione_diversa)) {
            throw new \InvalidArgumentException('non-nullable destinazione_diversa cannot be null');
        }
        $this->container['destinazione_diversa'] = $destinazione_diversa;

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
     * @param bool|null $iva_esclusa Indica se i prezzi inseriti sono da considerarsi al netto dell'IVA. Se `false`, l'IVA verrà scorporata in fase di elaborazione (app ufficiali).
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
     * Gets ritenuta_include_cassa_previdenziale
     *
     * @return bool|null
     */
    public function getRitenutaIncludeCassaPrevidenziale()
    {
        return $this->container['ritenuta_include_cassa_previdenziale'];
    }

    /**
     * Sets ritenuta_include_cassa_previdenziale
     *
     * @param bool|null $ritenuta_include_cassa_previdenziale Indica se la ritenuta deve essere applicata anche all'importo della cassa previdenziale riportato nel documento.
     *
     * @return self
     */
    public function setRitenutaIncludeCassaPrevidenziale($ritenuta_include_cassa_previdenziale)
    {
        if (is_null($ritenuta_include_cassa_previdenziale)) {
            throw new \InvalidArgumentException('non-nullable ritenuta_include_cassa_previdenziale cannot be null');
        }
        $this->container['ritenuta_include_cassa_previdenziale'] = $ritenuta_include_cassa_previdenziale;

        return $this;
    }

    /**
     * Gets classe_documento
     *
     * @return string|null
     */
    public function getClasseDocumento()
    {
        return $this->container['classe_documento'];
    }

    /**
     * Sets classe_documento
     *
     * @param string|null $classe_documento Classe del documento.
     *
     * @return self
     */
    public function setClasseDocumento($classe_documento)
    {
        if (is_null($classe_documento)) {
            throw new \InvalidArgumentException('non-nullable classe_documento cannot be null');
        }
        $allowedValues = $this->getClasseDocumentoAllowableValues();
        if (!in_array($classe_documento, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'classe_documento', must be one of '%s'",
                    $classe_documento,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['classe_documento'] = $classe_documento;

        return $this;
    }

    /**
     * Gets contatto
     *
     * @return int|null
     */
    public function getContatto()
    {
        return $this->container['contatto'];
    }

    /**
     * Sets contatto
     *
     * @param int|null $contatto Persona di contatto preferita, `AnagraficaNominativo1` oppure `AnagraficaNominativo2`.
     *
     * @return self
     */
    public function setContatto($contatto)
    {
        if (is_null($contatto)) {
            array_push($this->openAPINullablesSetToNull, 'contatto');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('contatto', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['contatto'] = $contatto;

        return $this;
    }

    /**
     * Gets numero_parte_testo
     *
     * @return string|null
     */
    public function getNumeroParteTesto()
    {
        return $this->container['numero_parte_testo'];
    }

    /**
     * Sets numero_parte_testo
     *
     * @param string|null $numero_parte_testo La parte alfanumerica del numero del documento, se presente. Ad esempio, \"bis\" per il documento num. 99/bis.
     *
     * @return self
     */
    public function setNumeroParteTesto($numero_parte_testo)
    {
        if (is_null($numero_parte_testo)) {
            array_push($this->openAPINullablesSetToNull, 'numero_parte_testo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('numero_parte_testo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($numero_parte_testo) && (mb_strlen($numero_parte_testo) > 40)) {
            throw new \InvalidArgumentException('invalid length for $numero_parte_testo when calling Documento., must be smaller than or equal to 40.');
        }
        if (!is_null($numero_parte_testo) && (mb_strlen($numero_parte_testo) < 0)) {
            throw new \InvalidArgumentException('invalid length for $numero_parte_testo when calling Documento., must be bigger than or equal to 0.');
        }

        $this->container['numero_parte_testo'] = $numero_parte_testo;

        return $this;
    }

    /**
     * Gets numero_parte_numerica
     *
     * @return int|null
     */
    public function getNumeroParteNumerica()
    {
        return $this->container['numero_parte_numerica'];
    }

    /**
     * Sets numero_parte_numerica
     *
     * @param int|null $numero_parte_numerica Il numero del documento, nella sua parte numerica. Ad esempio, 99 per il num. 99/bis.
     *
     * @return self
     */
    public function setNumeroParteNumerica($numero_parte_numerica)
    {
        if (is_null($numero_parte_numerica)) {
            throw new \InvalidArgumentException('non-nullable numero_parte_numerica cannot be null');
        }

        if (($numero_parte_numerica > 2147483647)) {
            throw new \InvalidArgumentException('invalid value for $numero_parte_numerica when calling Documento., must be smaller than or equal to 2147483647.');
        }
        if (($numero_parte_numerica < 0)) {
            throw new \InvalidArgumentException('invalid value for $numero_parte_numerica when calling Documento., must be bigger than or equal to 0.');
        }

        $this->container['numero_parte_numerica'] = $numero_parte_numerica;

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
     * @param \DateTime|null $data Data del documento.
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
     * Gets stato
     *
     * @return string|null
     */
    public function getStato()
    {
        return $this->container['stato'];
    }

    /**
     * Sets stato
     *
     * @param string|null $stato Lo stato corrente del documento. Ad esempio: Bozza, oppure Emesso.
     *
     * @return self
     */
    public function setStato($stato)
    {
        if (is_null($stato)) {
            throw new \InvalidArgumentException('non-nullable stato cannot be null');
        }
        $allowedValues = $this->getStatoAllowableValues();
        if (!in_array($stato, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'stato', must be one of '%s'",
                    $stato,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['stato'] = $stato;

        return $this;
    }

    /**
     * Gets data_scadenza_preventivo
     *
     * @return \DateTime|null
     */
    public function getDataScadenzaPreventivo()
    {
        return $this->container['data_scadenza_preventivo'];
    }

    /**
     * Sets data_scadenza_preventivo
     *
     * @param \DateTime|null $data_scadenza_preventivo La data di scadenza, nel caso in cui il documento sia un preventivo.
     *
     * @return self
     */
    public function setDataScadenzaPreventivo($data_scadenza_preventivo)
    {
        if (is_null($data_scadenza_preventivo)) {
            array_push($this->openAPINullablesSetToNull, 'data_scadenza_preventivo');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_scadenza_preventivo', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_scadenza_preventivo'] = $data_scadenza_preventivo;

        return $this;
    }

    /**
     * Gets data_inizio_scadenze
     *
     * @return \DateTime|null
     */
    public function getDataInizioScadenze()
    {
        return $this->container['data_inizio_scadenze'];
    }

    /**
     * Sets data_inizio_scadenze
     *
     * @param \DateTime|null $data_inizio_scadenze Data della prima scadenza di pagamento.
     *
     * @return self
     */
    public function setDataInizioScadenze($data_inizio_scadenze)
    {
        if (is_null($data_inizio_scadenze)) {
            array_push($this->openAPINullablesSetToNull, 'data_inizio_scadenze');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('data_inizio_scadenze', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['data_inizio_scadenze'] = $data_inizio_scadenze;

        return $this;
    }

    /**
     * Gets sconto_percentuale
     *
     * @return float|null
     */
    public function getScontoPercentuale()
    {
        return $this->container['sconto_percentuale'];
    }

    /**
     * Sets sconto_percentuale
     *
     * @param float|null $sconto_percentuale Sconto, in percentuale, da calcolare sull'intero importo del documento. Il valore è una frazione: `0.05` corrisponde al 5%.
     *
     * @return self
     */
    public function setScontoPercentuale($sconto_percentuale)
    {
        if (is_null($sconto_percentuale)) {
            throw new \InvalidArgumentException('non-nullable sconto_percentuale cannot be null');
        }
        $this->container['sconto_percentuale'] = $sconto_percentuale;

        return $this;
    }

    /**
     * Gets sconto_incondizionato_importo
     *
     * @return float|null
     */
    public function getScontoIncondizionatoImporto()
    {
        return $this->container['sconto_incondizionato_importo'];
    }

    /**
     * Sets sconto_incondizionato_importo
     *
     * @param float|null $sconto_incondizionato_importo Sconto, in valuta, da applicare all'intero importo del documento.
     *
     * @return self
     */
    public function setScontoIncondizionatoImporto($sconto_incondizionato_importo)
    {
        if (is_null($sconto_incondizionato_importo)) {
            throw new \InvalidArgumentException('non-nullable sconto_incondizionato_importo cannot be null');
        }
        $this->container['sconto_incondizionato_importo'] = $sconto_incondizionato_importo;

        return $this;
    }

    /**
     * Gets abbuono_importo
     *
     * @return float|null
     */
    public function getAbbuonoImporto()
    {
        return $this->container['abbuono_importo'];
    }

    /**
     * Sets abbuono_importo
     *
     * @param float|null $abbuono_importo Importo dell'abbuono.
     *
     * @return self
     */
    public function setAbbuonoImporto($abbuono_importo)
    {
        if (is_null($abbuono_importo)) {
            throw new \InvalidArgumentException('non-nullable abbuono_importo cannot be null');
        }
        $this->container['abbuono_importo'] = $abbuono_importo;

        return $this;
    }

    /**
     * Gets ritenuta_percentuale
     *
     * @return float|null
     */
    public function getRitenutaPercentuale()
    {
        return $this->container['ritenuta_percentuale'];
    }

    /**
     * Sets ritenuta_percentuale
     *
     * @param float|null $ritenuta_percentuale Quota della ritenuta da applicare al documento. Il valore è una frazione: `0.20` corrisponde al 20%.
     *
     * @return self
     */
    public function setRitenutaPercentuale($ritenuta_percentuale)
    {
        if (is_null($ritenuta_percentuale)) {
            throw new \InvalidArgumentException('non-nullable ritenuta_percentuale cannot be null');
        }
        $this->container['ritenuta_percentuale'] = $ritenuta_percentuale;

        return $this;
    }

    /**
     * Gets enasarco_percentuale
     *
     * @return float|null
     */
    public function getEnasarcoPercentuale()
    {
        return $this->container['enasarco_percentuale'];
    }

    /**
     * Sets enasarco_percentuale
     *
     * @param float|null $enasarco_percentuale Contributo Enasarco da applicare al documento.
     *
     * @return self
     */
    public function setEnasarcoPercentuale($enasarco_percentuale)
    {
        if (is_null($enasarco_percentuale)) {
            throw new \InvalidArgumentException('non-nullable enasarco_percentuale cannot be null');
        }
        $this->container['enasarco_percentuale'] = $enasarco_percentuale;

        return $this;
    }

    /**
     * Gets enasarco_importo
     *
     * @return float|null
     */
    public function getEnasarcoImporto()
    {
        return $this->container['enasarco_importo'];
    }

    /**
     * Sets enasarco_importo
     *
     * @param float|null $enasarco_importo Importo Enasarco.
     *
     * @return self
     */
    public function setEnasarcoImporto($enasarco_importo)
    {
        if (is_null($enasarco_importo)) {
            throw new \InvalidArgumentException('non-nullable enasarco_importo cannot be null');
        }
        $this->container['enasarco_importo'] = $enasarco_importo;

        return $this;
    }

    /**
     * Gets quota_ritenuta_su_imponibile
     *
     * @return float|null
     */
    public function getQuotaRitenutaSuImponibile()
    {
        return $this->container['quota_ritenuta_su_imponibile'];
    }

    /**
     * Sets quota_ritenuta_su_imponibile
     *
     * @param float|null $quota_ritenuta_su_imponibile Quota dell'imponibile su cui applicare la ritenuta. Il valore è una frazione: `0.50` corrisponde al 50%.
     *
     * @return self
     */
    public function setQuotaRitenutaSuImponibile($quota_ritenuta_su_imponibile)
    {
        if (is_null($quota_ritenuta_su_imponibile)) {
            throw new \InvalidArgumentException('non-nullable quota_ritenuta_su_imponibile cannot be null');
        }
        $this->container['quota_ritenuta_su_imponibile'] = $quota_ritenuta_su_imponibile;

        return $this;
    }

    /**
     * Gets cambio
     *
     * @return float|null
     */
    public function getCambio()
    {
        return $this->container['cambio'];
    }

    /**
     * Sets cambio
     *
     * @param float|null $cambio Tasso di cambio rispetto all'Euro, nel caso in cui la `Valuta` del documento non sia l'Euro.
     *
     * @return self
     */
    public function setCambio($cambio)
    {
        if (is_null($cambio)) {
            throw new \InvalidArgumentException('non-nullable cambio cannot be null');
        }
        $this->container['cambio'] = $cambio;

        return $this;
    }

    /**
     * Gets cassa_previdenziale_nome
     *
     * @return string|null
     */
    public function getCassaPrevidenzialeNome()
    {
        return $this->container['cassa_previdenziale_nome'];
    }

    /**
     * Sets cassa_previdenziale_nome
     *
     * @param string|null $cassa_previdenziale_nome Nome della cassa previdenziale applicata al documento.
     *
     * @return self
     */
    public function setCassaPrevidenzialeNome($cassa_previdenziale_nome)
    {
        if (is_null($cassa_previdenziale_nome)) {
            array_push($this->openAPINullablesSetToNull, 'cassa_previdenziale_nome');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cassa_previdenziale_nome', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        if (!is_null($cassa_previdenziale_nome) && (mb_strlen($cassa_previdenziale_nome) > 50)) {
            throw new \InvalidArgumentException('invalid length for $cassa_previdenziale_nome when calling Documento., must be smaller than or equal to 50.');
        }
        if (!is_null($cassa_previdenziale_nome) && (mb_strlen($cassa_previdenziale_nome) < 0)) {
            throw new \InvalidArgumentException('invalid length for $cassa_previdenziale_nome when calling Documento., must be bigger than or equal to 0.');
        }

        $this->container['cassa_previdenziale_nome'] = $cassa_previdenziale_nome;

        return $this;
    }

    /**
     * Gets cassa_previdenziale_percentuale
     *
     * @return float|null
     */
    public function getCassaPrevidenzialePercentuale()
    {
        return $this->container['cassa_previdenziale_percentuale'];
    }

    /**
     * Sets cassa_previdenziale_percentuale
     *
     * @param float|null $cassa_previdenziale_percentuale Aliquota (percentuale) della cassa previdenziale da applicare al documento. Il valore è una frazione: `0.04` corrisponde al 4%.
     *
     * @return self
     */
    public function setCassaPrevidenzialePercentuale($cassa_previdenziale_percentuale)
    {
        if (is_null($cassa_previdenziale_percentuale)) {
            throw new \InvalidArgumentException('non-nullable cassa_previdenziale_percentuale cannot be null');
        }
        $this->container['cassa_previdenziale_percentuale'] = $cassa_previdenziale_percentuale;

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
     * Gets sconto_pagamento_percentuale
     *
     * @return float|null
     */
    public function getScontoPagamentoPercentuale()
    {
        return $this->container['sconto_pagamento_percentuale'];
    }

    /**
     * Sets sconto_pagamento_percentuale
     *
     * @param float|null $sconto_pagamento_percentuale Sconto riconosciuto in base alla modalità di pagamento utilizzata (es: +5% in caso di rimessa diretta). Il valore è una frazione: `0.05` corrisponde al 5%.
     *
     * @return self
     */
    public function setScontoPagamentoPercentuale($sconto_pagamento_percentuale)
    {
        if (is_null($sconto_pagamento_percentuale)) {
            throw new \InvalidArgumentException('non-nullable sconto_pagamento_percentuale cannot be null');
        }
        $this->container['sconto_pagamento_percentuale'] = $sconto_pagamento_percentuale;

        return $this;
    }

    /**
     * Gets iva1_imponibile
     *
     * @return float|null
     */
    public function getIva1Imponibile()
    {
        return $this->container['iva1_imponibile'];
    }

    /**
     * Sets iva1_imponibile
     *
     * @param float|null $iva1_imponibile Totale imponibile attribuibile a `Iva1CausaleIva`
     *
     * @return self
     */
    public function setIva1Imponibile($iva1_imponibile)
    {
        if (is_null($iva1_imponibile)) {
            throw new \InvalidArgumentException('non-nullable iva1_imponibile cannot be null');
        }
        $this->container['iva1_imponibile'] = $iva1_imponibile;

        return $this;
    }

    /**
     * Gets iva2_imponibile
     *
     * @return float|null
     */
    public function getIva2Imponibile()
    {
        return $this->container['iva2_imponibile'];
    }

    /**
     * Sets iva2_imponibile
     *
     * @param float|null $iva2_imponibile Totale imponibile attribuibile a `Iva2CausaleIva`
     *
     * @return self
     */
    public function setIva2Imponibile($iva2_imponibile)
    {
        if (is_null($iva2_imponibile)) {
            throw new \InvalidArgumentException('non-nullable iva2_imponibile cannot be null');
        }
        $this->container['iva2_imponibile'] = $iva2_imponibile;

        return $this;
    }

    /**
     * Gets iva3_imponibile
     *
     * @return float|null
     */
    public function getIva3Imponibile()
    {
        return $this->container['iva3_imponibile'];
    }

    /**
     * Sets iva3_imponibile
     *
     * @param float|null $iva3_imponibile Totale imponibile attribuibile a `Iva3CausaleIva`
     *
     * @return self
     */
    public function setIva3Imponibile($iva3_imponibile)
    {
        if (is_null($iva3_imponibile)) {
            throw new \InvalidArgumentException('non-nullable iva3_imponibile cannot be null');
        }
        $this->container['iva3_imponibile'] = $iva3_imponibile;

        return $this;
    }

    /**
     * Gets iva4_imponibile
     *
     * @return float|null
     */
    public function getIva4Imponibile()
    {
        return $this->container['iva4_imponibile'];
    }

    /**
     * Sets iva4_imponibile
     *
     * @param float|null $iva4_imponibile Totale imponibile attribuibile a `Iva4CausaleIva`
     *
     * @return self
     */
    public function setIva4Imponibile($iva4_imponibile)
    {
        if (is_null($iva4_imponibile)) {
            throw new \InvalidArgumentException('non-nullable iva4_imponibile cannot be null');
        }
        $this->container['iva4_imponibile'] = $iva4_imponibile;

        return $this;
    }

    /**
     * Gets iva1_imponibile_lordo
     *
     * @return float|null
     */
    public function getIva1ImponibileLordo()
    {
        return $this->container['iva1_imponibile_lordo'];
    }

    /**
     * Sets iva1_imponibile_lordo
     *
     * @param float|null $iva1_imponibile_lordo Totale imponibile attribuibile a `Iva1CausaleIva` al lordo di eventuali sconti di fattura.
     *
     * @return self
     */
    public function setIva1ImponibileLordo($iva1_imponibile_lordo)
    {
        if (is_null($iva1_imponibile_lordo)) {
            throw new \InvalidArgumentException('non-nullable iva1_imponibile_lordo cannot be null');
        }
        $this->container['iva1_imponibile_lordo'] = $iva1_imponibile_lordo;

        return $this;
    }

    /**
     * Gets iva2_imponibile_lordo
     *
     * @return float|null
     */
    public function getIva2ImponibileLordo()
    {
        return $this->container['iva2_imponibile_lordo'];
    }

    /**
     * Sets iva2_imponibile_lordo
     *
     * @param float|null $iva2_imponibile_lordo Totale imponibile attribuibile a `Iva2CausaleIva` al lordo di eventuali sconti di fattura.
     *
     * @return self
     */
    public function setIva2ImponibileLordo($iva2_imponibile_lordo)
    {
        if (is_null($iva2_imponibile_lordo)) {
            throw new \InvalidArgumentException('non-nullable iva2_imponibile_lordo cannot be null');
        }
        $this->container['iva2_imponibile_lordo'] = $iva2_imponibile_lordo;

        return $this;
    }

    /**
     * Gets iva3_imponibile_lordo
     *
     * @return float|null
     */
    public function getIva3ImponibileLordo()
    {
        return $this->container['iva3_imponibile_lordo'];
    }

    /**
     * Sets iva3_imponibile_lordo
     *
     * @param float|null $iva3_imponibile_lordo Totale imponibile attribuibile a `Iva3CausaleIva` al lordo di eventuali sconti di fattura.
     *
     * @return self
     */
    public function setIva3ImponibileLordo($iva3_imponibile_lordo)
    {
        if (is_null($iva3_imponibile_lordo)) {
            throw new \InvalidArgumentException('non-nullable iva3_imponibile_lordo cannot be null');
        }
        $this->container['iva3_imponibile_lordo'] = $iva3_imponibile_lordo;

        return $this;
    }

    /**
     * Gets iva4_imponibile_lordo
     *
     * @return float|null
     */
    public function getIva4ImponibileLordo()
    {
        return $this->container['iva4_imponibile_lordo'];
    }

    /**
     * Sets iva4_imponibile_lordo
     *
     * @param float|null $iva4_imponibile_lordo Totale imponibile attribuibile a `Iva4CausaleIva` al lordo di eventuali sconti di fattura.
     *
     * @return self
     */
    public function setIva4ImponibileLordo($iva4_imponibile_lordo)
    {
        if (is_null($iva4_imponibile_lordo)) {
            throw new \InvalidArgumentException('non-nullable iva4_imponibile_lordo cannot be null');
        }
        $this->container['iva4_imponibile_lordo'] = $iva4_imponibile_lordo;

        return $this;
    }

    /**
     * Gets iva1_imposta
     *
     * @return float|null
     */
    public function getIva1Imposta()
    {
        return $this->container['iva1_imposta'];
    }

    /**
     * Sets iva1_imposta
     *
     * @param float|null $iva1_imposta Totale imposta per `Iva1CausaleIva`
     *
     * @return self
     */
    public function setIva1Imposta($iva1_imposta)
    {
        if (is_null($iva1_imposta)) {
            throw new \InvalidArgumentException('non-nullable iva1_imposta cannot be null');
        }
        $this->container['iva1_imposta'] = $iva1_imposta;

        return $this;
    }

    /**
     * Gets iva2_imposta
     *
     * @return float|null
     */
    public function getIva2Imposta()
    {
        return $this->container['iva2_imposta'];
    }

    /**
     * Sets iva2_imposta
     *
     * @param float|null $iva2_imposta Totale imposta per `Iva2CausaleIva`
     *
     * @return self
     */
    public function setIva2Imposta($iva2_imposta)
    {
        if (is_null($iva2_imposta)) {
            throw new \InvalidArgumentException('non-nullable iva2_imposta cannot be null');
        }
        $this->container['iva2_imposta'] = $iva2_imposta;

        return $this;
    }

    /**
     * Gets iva3_imposta
     *
     * @return float|null
     */
    public function getIva3Imposta()
    {
        return $this->container['iva3_imposta'];
    }

    /**
     * Sets iva3_imposta
     *
     * @param float|null $iva3_imposta Totale imposta per `Iva3CausaleIva`
     *
     * @return self
     */
    public function setIva3Imposta($iva3_imposta)
    {
        if (is_null($iva3_imposta)) {
            throw new \InvalidArgumentException('non-nullable iva3_imposta cannot be null');
        }
        $this->container['iva3_imposta'] = $iva3_imposta;

        return $this;
    }

    /**
     * Gets iva4_imposta
     *
     * @return float|null
     */
    public function getIva4Imposta()
    {
        return $this->container['iva4_imposta'];
    }

    /**
     * Sets iva4_imposta
     *
     * @param float|null $iva4_imposta Totale imposta per `Iva4CausaleIva`
     *
     * @return self
     */
    public function setIva4Imposta($iva4_imposta)
    {
        if (is_null($iva4_imposta)) {
            throw new \InvalidArgumentException('non-nullable iva4_imposta cannot be null');
        }
        $this->container['iva4_imposta'] = $iva4_imposta;

        return $this;
    }

    /**
     * Gets totale_ritenuta
     *
     * @return float|null
     */
    public function getTotaleRitenuta()
    {
        return $this->container['totale_ritenuta'];
    }

    /**
     * Sets totale_ritenuta
     *
     * @param float|null $totale_ritenuta Importo della ritenuta.
     *
     * @return self
     */
    public function setTotaleRitenuta($totale_ritenuta)
    {
        if (is_null($totale_ritenuta)) {
            throw new \InvalidArgumentException('non-nullable totale_ritenuta cannot be null');
        }
        $this->container['totale_ritenuta'] = $totale_ritenuta;

        return $this;
    }

    /**
     * Gets totale_cassa_previdenziale
     *
     * @return float|null
     */
    public function getTotaleCassaPrevidenziale()
    {
        return $this->container['totale_cassa_previdenziale'];
    }

    /**
     * Sets totale_cassa_previdenziale
     *
     * @param float|null $totale_cassa_previdenziale Importo della cassa previdenziale.
     *
     * @return self
     */
    public function setTotaleCassaPrevidenziale($totale_cassa_previdenziale)
    {
        if (is_null($totale_cassa_previdenziale)) {
            throw new \InvalidArgumentException('non-nullable totale_cassa_previdenziale cannot be null');
        }
        $this->container['totale_cassa_previdenziale'] = $totale_cassa_previdenziale;

        return $this;
    }

    /**
     * Gets totale_spese
     *
     * @return float|null
     */
    public function getTotaleSpese()
    {
        return $this->container['totale_spese'];
    }

    /**
     * Sets totale_spese
     *
     * @param float|null $totale_spese Totale delle spese esenti e non esenti.
     *
     * @return self
     */
    public function setTotaleSpese($totale_spese)
    {
        if (is_null($totale_spese)) {
            throw new \InvalidArgumentException('non-nullable totale_spese cannot be null');
        }
        $this->container['totale_spese'] = $totale_spese;

        return $this;
    }

    /**
     * Gets totale_spese_esenti
     *
     * @return float|null
     */
    public function getTotaleSpeseEsenti()
    {
        return $this->container['totale_spese_esenti'];
    }

    /**
     * Sets totale_spese_esenti
     *
     * @param float|null $totale_spese_esenti Totale delle spese esenti da IVA.
     *
     * @return self
     */
    public function setTotaleSpeseEsenti($totale_spese_esenti)
    {
        if (is_null($totale_spese_esenti)) {
            throw new \InvalidArgumentException('non-nullable totale_spese_esenti cannot be null');
        }
        $this->container['totale_spese_esenti'] = $totale_spese_esenti;

        return $this;
    }

    /**
     * Gets totale_spese_non_esenti
     *
     * @return float|null
     */
    public function getTotaleSpeseNonEsenti()
    {
        return $this->container['totale_spese_non_esenti'];
    }

    /**
     * Sets totale_spese_non_esenti
     *
     * @param float|null $totale_spese_non_esenti Totale delle spese non esenti da IVA.
     *
     * @return self
     */
    public function setTotaleSpeseNonEsenti($totale_spese_non_esenti)
    {
        if (is_null($totale_spese_non_esenti)) {
            throw new \InvalidArgumentException('non-nullable totale_spese_non_esenti cannot be null');
        }
        $this->container['totale_spese_non_esenti'] = $totale_spese_non_esenti;

        return $this;
    }

    /**
     * Gets totale_merce
     *
     * @return float|null
     */
    public function getTotaleMerce()
    {
        return $this->container['totale_merce'];
    }

    /**
     * Sets totale_merce
     *
     * @param float|null $totale_merce Importo della merce.
     *
     * @return self
     */
    public function setTotaleMerce($totale_merce)
    {
        if (is_null($totale_merce)) {
            throw new \InvalidArgumentException('non-nullable totale_merce cannot be null');
        }
        $this->container['totale_merce'] = $totale_merce;

        return $this;
    }

    /**
     * Gets totale_merce_scontata
     *
     * @return float|null
     */
    public function getTotaleMerceScontata()
    {
        return $this->container['totale_merce_scontata'];
    }

    /**
     * Sets totale_merce_scontata
     *
     * @param float|null $totale_merce_scontata Importo della merce scontata.
     *
     * @return self
     */
    public function setTotaleMerceScontata($totale_merce_scontata)
    {
        if (is_null($totale_merce_scontata)) {
            throw new \InvalidArgumentException('non-nullable totale_merce_scontata cannot be null');
        }
        $this->container['totale_merce_scontata'] = $totale_merce_scontata;

        return $this;
    }

    /**
     * Gets totale_imponibile
     *
     * @return float|null
     */
    public function getTotaleImponibile()
    {
        return $this->container['totale_imponibile'];
    }

    /**
     * Sets totale_imponibile
     *
     * @param float|null $totale_imponibile Totale imponibile, considerando tutte le causali IVA coinvolte.
     *
     * @return self
     */
    public function setTotaleImponibile($totale_imponibile)
    {
        if (is_null($totale_imponibile)) {
            throw new \InvalidArgumentException('non-nullable totale_imponibile cannot be null');
        }
        $this->container['totale_imponibile'] = $totale_imponibile;

        return $this;
    }

    /**
     * Gets totale_imposta
     *
     * @return float|null
     */
    public function getTotaleImposta()
    {
        return $this->container['totale_imposta'];
    }

    /**
     * Sets totale_imposta
     *
     * @param float|null $totale_imposta Imposta IVA.
     *
     * @return self
     */
    public function setTotaleImposta($totale_imposta)
    {
        if (is_null($totale_imposta)) {
            throw new \InvalidArgumentException('non-nullable totale_imposta cannot be null');
        }
        $this->container['totale_imposta'] = $totale_imposta;

        return $this;
    }

    /**
     * Gets totale_esente
     *
     * @return float|null
     */
    public function getTotaleEsente()
    {
        return $this->container['totale_esente'];
    }

    /**
     * Sets totale_esente
     *
     * @param float|null $totale_esente Importi esenti da IVA.
     *
     * @return self
     */
    public function setTotaleEsente($totale_esente)
    {
        if (is_null($totale_esente)) {
            throw new \InvalidArgumentException('non-nullable totale_esente cannot be null');
        }
        $this->container['totale_esente'] = $totale_esente;

        return $this;
    }

    /**
     * Gets totale_documento
     *
     * @return float|null
     */
    public function getTotaleDocumento()
    {
        return $this->container['totale_documento'];
    }

    /**
     * Sets totale_documento
     *
     * @param float|null $totale_documento Importo totale del documento.
     *
     * @return self
     */
    public function setTotaleDocumento($totale_documento)
    {
        if (is_null($totale_documento)) {
            throw new \InvalidArgumentException('non-nullable totale_documento cannot be null');
        }
        $this->container['totale_documento'] = $totale_documento;

        return $this;
    }

    /**
     * Gets netto_a_pagare
     *
     * @return float|null
     */
    public function getNettoAPagare()
    {
        return $this->container['netto_a_pagare'];
    }

    /**
     * Sets netto_a_pagare
     *
     * @param float|null $netto_a_pagare Netto a pagare.
     *
     * @return self
     */
    public function setNettoAPagare($netto_a_pagare)
    {
        if (is_null($netto_a_pagare)) {
            throw new \InvalidArgumentException('non-nullable netto_a_pagare cannot be null');
        }
        $this->container['netto_a_pagare'] = $netto_a_pagare;

        return $this;
    }

    /**
     * Gets totale_split_payment
     *
     * @return float|null
     */
    public function getTotaleSplitPayment()
    {
        return $this->container['totale_split_payment'];
    }

    /**
     * Sets totale_split_payment
     *
     * @param float|null $totale_split_payment Importo dello split payment.
     *
     * @return self
     */
    public function setTotaleSplitPayment($totale_split_payment)
    {
        if (is_null($totale_split_payment)) {
            throw new \InvalidArgumentException('non-nullable totale_split_payment cannot be null');
        }
        $this->container['totale_split_payment'] = $totale_split_payment;

        return $this;
    }

    /**
     * Gets bollo
     *
     * @return float|null
     */
    public function getBollo()
    {
        return $this->container['bollo'];
    }

    /**
     * Sets bollo
     *
     * @param float|null $bollo Importo del bollo.
     *
     * @return self
     */
    public function setBollo($bollo)
    {
        if (is_null($bollo)) {
            throw new \InvalidArgumentException('non-nullable bollo cannot be null');
        }
        $this->container['bollo'] = $bollo;

        return $this;
    }

    /**
     * Gets xml_file_name
     *
     * @return string|null
     */
    public function getXmlFileName()
    {
        return $this->container['xml_file_name'];
    }

    /**
     * Sets xml_file_name
     *
     * @param string|null $xml_file_name Nome del file XML.
     *
     * @return self
     */
    public function setXmlFileName($xml_file_name)
    {
        if (is_null($xml_file_name)) {
            array_push($this->openAPINullablesSetToNull, 'xml_file_name');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('xml_file_name', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['xml_file_name'] = $xml_file_name;

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
     * @param string|null $riferimento_amministrazione Numero di riferimento dell'amministrazione.
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
            throw new \InvalidArgumentException('invalid length for $riferimento_amministrazione when calling Documento., must be smaller than or equal to 20.');
        }
        if (!is_null($riferimento_amministrazione) && (mb_strlen($riferimento_amministrazione) < 0)) {
            throw new \InvalidArgumentException('invalid length for $riferimento_amministrazione when calling Documento., must be bigger than or equal to 0.');
        }

        $this->container['riferimento_amministrazione'] = $riferimento_amministrazione;

        return $this;
    }

    /**
     * Gets banca
     *
     * @return \Amica\Sdk\Model\BancaDocumento|null
     */
    public function getBanca()
    {
        return $this->container['banca'];
    }

    /**
     * Sets banca
     *
     * @param \Amica\Sdk\Model\BancaDocumento|null $banca banca
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
     * Gets dati_trasporto
     *
     * @return \Amica\Sdk\Model\DatiTrasporto|null
     */
    public function getDatiTrasporto()
    {
        return $this->container['dati_trasporto'];
    }

    /**
     * Sets dati_trasporto
     *
     * @param \Amica\Sdk\Model\DatiTrasporto|null $dati_trasporto dati_trasporto
     *
     * @return self
     */
    public function setDatiTrasporto($dati_trasporto)
    {
        if (is_null($dati_trasporto)) {
            throw new \InvalidArgumentException('non-nullable dati_trasporto cannot be null');
        }
        $this->container['dati_trasporto'] = $dati_trasporto;

        return $this;
    }

    /**
     * Gets dati_sdi
     *
     * @return \Amica\Sdk\Model\DatiSdi|null
     */
    public function getDatiSdi()
    {
        return $this->container['dati_sdi'];
    }

    /**
     * Sets dati_sdi
     *
     * @param \Amica\Sdk\Model\DatiSdi|null $dati_sdi dati_sdi
     *
     * @return self
     */
    public function setDatiSdi($dati_sdi)
    {
        if (is_null($dati_sdi)) {
            throw new \InvalidArgumentException('non-nullable dati_sdi cannot be null');
        }
        $this->container['dati_sdi'] = $dati_sdi;

        return $this;
    }

    /**
     * Gets dati_pa
     *
     * @return \Amica\Sdk\Model\DatiPA|null
     */
    public function getDatiPa()
    {
        return $this->container['dati_pa'];
    }

    /**
     * Sets dati_pa
     *
     * @param \Amica\Sdk\Model\DatiPA|null $dati_pa dati_pa
     *
     * @return self
     */
    public function setDatiPa($dati_pa)
    {
        if (is_null($dati_pa)) {
            throw new \InvalidArgumentException('non-nullable dati_pa cannot be null');
        }
        $this->container['dati_pa'] = $dati_pa;

        return $this;
    }

    /**
     * Gets righe
     *
     * @return \Amica\Sdk\Model\BeneServizio[]|null
     */
    public function getRighe()
    {
        return $this->container['righe'];
    }

    /**
     * Sets righe
     *
     * @param \Amica\Sdk\Model\BeneServizio[]|null $righe Righe del documento.
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
     * Gets spese_documento
     *
     * @return \Amica\Sdk\Model\SpesaDocumento[]|null
     */
    public function getSpeseDocumento()
    {
        return $this->container['spese_documento'];
    }

    /**
     * Sets spese_documento
     *
     * @param \Amica\Sdk\Model\SpesaDocumento[]|null $spese_documento Elenco delle spese.
     *
     * @return self
     */
    public function setSpeseDocumento($spese_documento)
    {
        if (is_null($spese_documento)) {
            array_push($this->openAPINullablesSetToNull, 'spese_documento');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('spese_documento', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['spese_documento'] = $spese_documento;

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
     * @param \Amica\Sdk\Model\Scadenza[]|null $scadenze Elenco delle scadenze.
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
     * Gets numero
     *
     * @return string|null
     */
    public function getNumero()
    {
        return $this->container['numero'];
    }

    /**
     * Sets numero
     *
     * @param string|null $numero Numero completo del documento. Ad esempio, `99/bis`. Generato a partire da `NumeroParteNumerica` e `NumeroParteTesto`. Nota bene: proprietà valorizzata solo nel caso di un'istanza salvata sul backend.
     *
     * @return self
     */
    public function setNumero($numero)
    {
        if (is_null($numero)) {
            array_push($this->openAPINullablesSetToNull, 'numero');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('numero', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['numero'] = $numero;

        return $this;
    }

    /**
     * Gets media_file
     *
     * @return \Amica\Sdk\Model\DocumentoMediaFile|null
     */
    public function getMediaFile()
    {
        return $this->container['media_file'];
    }

    /**
     * Sets media_file
     *
     * @param \Amica\Sdk\Model\DocumentoMediaFile|null $media_file media_file
     *
     * @return self
     */
    public function setMediaFile($media_file)
    {
        if (is_null($media_file)) {
            throw new \InvalidArgumentException('non-nullable media_file cannot be null');
        }
        $this->container['media_file'] = $media_file;

        return $this;
    }

    /**
     * Gets allegato
     *
     * @return string|null
     */
    public function getAllegato()
    {
        return $this->container['allegato'];
    }

    /**
     * Sets allegato
     *
     * @param string|null $allegato Obsoleto. Non utilizzare.
     *
     * @return self
     */
    public function setAllegato($allegato)
    {
        if (is_null($allegato)) {
            array_push($this->openAPINullablesSetToNull, 'allegato');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('allegato', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['allegato'] = $allegato;

        return $this;
    }

    /**
     * Gets bonus_fiscale_causale
     *
     * @return string|null
     */
    public function getBonusFiscaleCausale()
    {
        return $this->container['bonus_fiscale_causale'];
    }

    /**
     * Sets bonus_fiscale_causale
     *
     * @param string|null $bonus_fiscale_causale Causale del bonus fiscale.
     *
     * @return self
     */
    public function setBonusFiscaleCausale($bonus_fiscale_causale)
    {
        if (is_null($bonus_fiscale_causale)) {
            array_push($this->openAPINullablesSetToNull, 'bonus_fiscale_causale');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('bonus_fiscale_causale', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['bonus_fiscale_causale'] = $bonus_fiscale_causale;

        return $this;
    }

    /**
     * Gets bonus_fiscale_importo
     *
     * @return float|null
     */
    public function getBonusFiscaleImporto()
    {
        return $this->container['bonus_fiscale_importo'];
    }

    /**
     * Sets bonus_fiscale_importo
     *
     * @param float|null $bonus_fiscale_importo Importo del bonus fiscale.
     *
     * @return self
     */
    public function setBonusFiscaleImporto($bonus_fiscale_importo)
    {
        if (is_null($bonus_fiscale_importo)) {
            throw new \InvalidArgumentException('non-nullable bonus_fiscale_importo cannot be null');
        }
        $this->container['bonus_fiscale_importo'] = $bonus_fiscale_importo;

        return $this;
    }

    /**
     * Gets bonus_fiscale_percentuale
     *
     * @return float|null
     */
    public function getBonusFiscalePercentuale()
    {
        return $this->container['bonus_fiscale_percentuale'];
    }

    /**
     * Sets bonus_fiscale_percentuale
     *
     * @param float|null $bonus_fiscale_percentuale Percentuale del bonus fiscale. Il valore è una frazione: `0.50` corrisponde al 50%.
     *
     * @return self
     */
    public function setBonusFiscalePercentuale($bonus_fiscale_percentuale)
    {
        if (is_null($bonus_fiscale_percentuale)) {
            throw new \InvalidArgumentException('non-nullable bonus_fiscale_percentuale cannot be null');
        }
        $this->container['bonus_fiscale_percentuale'] = $bonus_fiscale_percentuale;

        return $this;
    }

    /**
     * Gets progressivo_invio
     *
     * @return \Amica\Sdk\Model\ProgressivoInvio[]|null
     */
    public function getProgressivoInvio()
    {
        return $this->container['progressivo_invio'];
    }

    /**
     * Sets progressivo_invio
     *
     * @param \Amica\Sdk\Model\ProgressivoInvio[]|null $progressivo_invio Numeri di invio (progressivi), così come restituiti dallo Sdi.
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
        $this->container['progressivo_invio'] = $progressivo_invio;

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


