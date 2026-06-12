<?php
/**
 * SpesaDocumentoApi
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
 * L'Amica Cloud API fornisce accesso programmatico a tutti i dati aziendali gestiti da [Amica 20](https://gestionaleamica.com). Supporta il ciclo completo di documenti, fatture, magazzino, contabilità e anagrafiche. Per guide e documentazione completa, visitare [amica.dev](https://amica.dev/).  ## Autenticazione  Tutte le richieste API richiedono un token OAuth 2.0 Bearer valido, da includere nell'header `Authorization`:  ``` Authorization: Bearer <token> ```  ### Registra la tua app  Per integrare l'Amica Cloud API registra la tua applicazione sull'**App Store di Amica**. Al termine della registrazione ottieni una coppia di credenziali OAuth 2.0 — `client_id` e `client_secret` (il secret è mostrato **una sola volta**) — e scegli gli **scope** e il **flusso di autenticazione** desiderati.  ### Scegli il flusso  - **Authorization Code con PKCE** (consigliato per app che operano per conto di un utente Amica): l'utente autorizza la tua app tramite la pagina di consenso dell'identity provider; la tua app riceve un `code` da scambiare con un access token.    ```   GET  {identity-provider}/connect/authorize?response_type=code&client_id=<client_id>&redirect_uri=<redirect_uri>&scope=openid%20read%20write%20documenti&code_challenge=<challenge>&code_challenge_method=S256   POST {identity-provider}/connect/token        grant_type=authorization_code&code=<code>&code_verifier=<verifier>&client_id=<client_id>&client_secret=<client_secret>&redirect_uri=<redirect_uri>   ```  - **Client Credentials** (per integrazioni server-to-server, senza utente interattivo):    ```   POST {identity-provider}/connect/token   Content-Type: application/x-www-form-urlencoded    grant_type=client_credentials&client_id=<client_id>&client_secret=<client_secret>&scope=read write documenti contabilita   ```    > **Nota:** il flusso Client Credentials è disponibile solo per le **app private**, non per quelle pubblicate sul marketplace.  In entrambi i casi la risposta contiene l'`access_token` (JWT con `aud: amica-cloud`) da usare nelle chiamate successive finché non scade (`expires_in`), dopodiché va richiesto un nuovo token. Ogni token contiene uno o più **scope** che determinano le aree accessibili (vedi sotto). Dalla [API Reference](https://amica.dev) è possibile incollare un access token per provare gli endpoint direttamente dal browser.  ## Scope di autorizzazione  L'accesso agli endpoint è governato dagli scope presenti nel token OAuth:  | Scope | Descrizione | |-|-| | `comune` | Anagrafiche: contatti, indirizzi, nazioni, valute, modalità di pagamento, ecc. | | `magazzino` | Magazzino: articoli, listini, movimenti di magazzino, giacenze, distinte base. | | `contabilita` | Contabilità: registrazioni contabili, scadenze, castelletti IVA, fatture elettroniche XML. | | `documenti` | Documenti: fatture, note di credito, DDT, ordini, preventivi, proforma ed emissione. |  Una richiesta verso un endpoint non coperto dallo scope del token riceverà una risposta `403 Forbidden`.  ## Company ID  La maggior parte degli endpoint è relativa a una specifica azienda e segue il pattern:  ``` /api/v3/{companyId}/risorsa ```  Il `companyId` è l'identificativo univoco di ciascun database aziendale. Usare l'endpoint `GET /api/v3/config/Azienda` per ottenere l'elenco delle aziende disponibili.  ## Paginazione  Gli endpoint che restituiscono liste supportano la paginazione tramite query parameter:  | Parametro | Descrizione | |-|-| | `Skip` | Numero di elementi da saltare dall'inizio del set di risultati. | | `Take` | Numero di elementi da includere nella risposta. |  La risposta include due header:  | Header | Descrizione | |-|-| | `X-Read-TotalCount` | Numero totale di elementi che soddisfano il filtro corrente. | | `X-Read-Count` | Numero di elementi effettivamente restituiti nel body della risposta. |  ## Ordinamento e filtri  Gli endpoint lista accettano parametri aggiuntivi:  | Parametro | Descrizione | |-|-| | `Order` | Lista di proprietà per l'ordinamento, separate da virgola. Un segno meno (`-`) prima del nome indica ordine discendente. Es. `-RagioneSociale1,Citta`. | | `Filter` | Condizioni di ricerca: lista di espressioni separate da virgola, ciascuna formata da nome campo, operatore, valore ed eventuale connettore logico. Operatori: `$eq`, `$ne`, `$gt`, `$lt`, `$lte`, `$gte`, `$ct` (contiene), `$nct`, `$sw` (inizia con), `$ew` (finisce con). Connettori: `$and` (default, omissibile) e `$or`; le sotto-query si racchiudono tra `[]`. Es. `RagioneSociale1 $ct acme $or, Fido $gt 1000`. | | `SkipTotalCount` | Se `true`, l'header `X-Read-TotalCount` viene omesso dalla risposta: migliori prestazioni a scapito del conteggio totale. Default `false`. |  ## Espansione oggetti correlati  Gli endpoint singoli e lista supportano il parametro `Expand`. Quando fornito, gli oggetti correlati vengono inclusi inline nella risposta anziché restituiti come riferimenti. È possibile specificare espansioni multiple separate da virgola.  ## Gestione errori  L'API usa codici di stato HTTP standard e restituisce [RFC 7807](https://tools.ietf.org/html/rfc7807) Problem Details in caso di errore:  | Codice | Significato | |-|-| | `400` | Bad Request — validazione fallita o parametro obbligatorio mancante. | | `401` | Unauthorized — token mancante o scaduto. | | `403` | Forbidden — il token non possiede lo scope richiesto. | | `404` | Not Found — la risorsa richiesta non esiste. | | `409` | Conflict — conflitto di concorrenza. | | `422` | Unprocessable Entity — entità non processabile (es. token di concorrenza obsoleto). |  Alcune risposte `400` includono un campo `overridable` che indica se l'errore di validazione può essere ignorato impostando il parametro `validationOverride` a `true`.  ## Controllo della concorrenza  Le entità contengono una proprietà `ConcurrencyToken`. Quando si aggiorna un'entità tramite `PUT`, il token nel body della richiesta viene confrontato con il valore memorizzato. Se differiscono, viene restituito un `422 Unprocessable Entity`. Usare sempre l'ultimo token ricevuto da una risposta `GET` o `POST`.
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

namespace Amica\Sdk\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Amica\Sdk\ApiException;
use Amica\Sdk\Configuration;
use Amica\Sdk\HeaderSelector;
use Amica\Sdk\ObjectSerializer;

/**
 * SpesaDocumentoApi Class Doc Comment
 *
 * @category Class
 * @package  Amica\Sdk
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class SpesaDocumentoApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @var int Host index
     */
    protected $hostIndex;

    /** @var string[] $contentTypes **/
    public const contentTypes = [
        'apiVversionCompanyIdSpesaDocumentoGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaDocumentoIdDelete' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaDocumentoIdGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaDocumentoIdPut' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
        'apiVversionCompanyIdSpesaDocumentoNewGet' => [
            'application/json',
        ],
        'apiVversionCompanyIdSpesaDocumentoPost' => [
            'application/json',
            'text/json',
            'application/*+json',
        ],
    ];

    /**
     * @param ClientInterface $client
     * @param Configuration   $config
     * @param HeaderSelector  $selector
     * @param int             $hostIndex (Optional) host index to select the list of hosts if defined in the OpenAPI spec
     */
    public function __construct(
        ?ClientInterface $client = null,
        ?Configuration $config = null,
        ?HeaderSelector $selector = null,
        int $hostIndex = 0
    ) {
        $this->client = $client ?: new Client();
        $this->config = $config ?: Configuration::getDefaultConfiguration();
        $this->headerSelector = $selector ?: new HeaderSelector();
        $this->hostIndex = $hostIndex;
    }

    /**
     * Set the host index
     *
     * @param int $hostIndex Host index (required)
     */
    public function setHostIndex($hostIndex): void
    {
        $this->hostIndex = $hostIndex;
    }

    /**
     * Get the host index
     *
     * @return int Host index
     */
    public function getHostIndex()
    {
        return $this->hostIndex;
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoGet
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento[]|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoGet($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoGetWithHttpInfo($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoGetWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento[]|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoGetWithHttpInfo($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoGetRequest($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\SpesaDocumento[]' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento[]' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento[]', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento[]';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento[]',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoGetAsync
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoGetAsync($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoGetAsyncWithHttpInfo($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoGetAsyncWithHttpInfo
     *
     * Restituisce una lista di elementi.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoGetAsyncWithHttpInfo($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento[]';
        $request = $this->apiVversionCompanyIdSpesaDocumentoGetRequest($version, $company_id, $skip, $take, $skip_total_count, $filter, $order, $expand, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int|null $skip Numero di elementi da escludere dall&#39;inizio del risultato. Combinato con &#x60;Take&#x60;, è utile per la paginazione. (optional)
     * @param  int|null $take Numero di elementi da includere nel risultato. Combinato con &#x60;Skip&#x60;, è utile per la paginazione. (optional)
     * @param  bool|null $skip_total_count Indica se l&#39;header &#x60;X-Read-TotalCount&#x60; debba essere escluso dalla risposta. Impostare a &#x60;true&#x60; per prestazioni migliori a scapito dell&#39;accuratezza. Il valore predefinito è &#x60;false&#x60;. (optional)
     * @param  string|null $filter Condizioni di ricerca. Elenco di espressioni separate da virgola. Ogni espressione è composta da nome del campo, operatore di confronto, valore ed eventualmente un connettore logico. Operatori validi: &#x60;$eq&#x60; (uguale), &#x60;$ne&#x60; (diverso), &#x60;$gt&#x60; (maggiore di), &#x60;$lt&#x60; (minore di), &#x60;$lte&#x60; (minore o uguale), &#x60;$gte&#x60; (maggiore o uguale), &#x60;$ct&#x60; (contiene), &#x60;$nct&#x60; (non contiene), &#x60;$sw&#x60; (inizia con), &#x60;$ew&#x60; (finisce con). Più espressioni possono essere concatenate con un connettore logico &#x60;$and&#x60; o &#x60;$or&#x60; (&#x60;$and&#x60; può essere omesso). Le query annidate si esprimono racchiudendo le sotto-query tra parentesi quadre (&#x60;[]&#x60;). Nell&#39;esempio vengono inclusi tutti gli elementi con &#x60;field1&#x3D;\&quot;hello\&quot;&#x60; oppure &#x60;field2&gt;10&#x60;. (optional)
     * @param  string|null $order Opzioni di ordinamento. Un segno meno (trattino) prima del nome indica ordinamento discendente. Nell&#39;esempio i risultati sono ordinati prima per &#x60;field1&#x60; (discendente), poi per &#x60;field2&#x60;. (optional)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoGetRequest($version, $company_id, $skip = null, $take = null, $skip_total_count = null, $filter = null, $order = null, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoGet'
            );
        }








        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $skip,
            'Skip', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $take,
            'Take', // param base name
            'integer', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $skip_total_count,
            'SkipTotalCount', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $filter,
            'Filter', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $order,
            'Order', // param base name
            'string', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);
        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $expand,
            'Expand', // param base name
            'array', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdDelete
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoIdDelete($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoIdDeleteWithHttpInfo($version, $company_id, $id, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdDeleteWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoIdDeleteWithHttpInfo($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdDeleteRequest($version, $company_id, $id, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\SpesaDocumento' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdDeleteAsync
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdDeleteAsync($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoIdDeleteAsyncWithHttpInfo($version, $company_id, $id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdDeleteAsyncWithHttpInfo
     *
     * Elimina un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdDeleteAsyncWithHttpInfo($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento';
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdDeleteRequest($version, $company_id, $id, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoIdDelete'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da eliminare. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoIdDeleteRequest($version, $company_id, $id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdDelete'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoIdDelete'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoIdDelete'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaDocumentoIdDelete'
            );
        }


        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'DELETE',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdGet
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoIdGet($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoIdGetWithHttpInfo($version, $company_id, $id, $expand, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdGetWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoIdGetWithHttpInfo($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdGetRequest($version, $company_id, $id, $expand, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\SpesaDocumento' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdGetAsync
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdGetAsync($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoIdGetAsyncWithHttpInfo($version, $company_id, $id, $expand, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdGetAsyncWithHttpInfo
     *
     * Restituisce un elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdGetAsyncWithHttpInfo($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento';
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdGetRequest($version, $company_id, $id, $expand, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoIdGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID dell&#39;elemento da recuperare. (required)
     * @param  string[]|null $expand Elenco separato da virgole di oggetti da espandere. Permette di recuperare gli oggetti correlati in un&#39;unica chiamata. Ad esempio, per includere i beni e servizi associati a ciascun documento si aggiunge &#x60;expand&#x3D;Rows&#x60; alla chiamata. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoIdGetRequest($version, $company_id, $id, $expand = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoIdGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoIdGet'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaDocumentoIdGet'
            );
        }



        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $expand,
            'Expand', // param base name
            'array', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdPut
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoIdPut($version, $company_id, $id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoIdPutWithHttpInfo($version, $company_id, $id, $validation_override, $spesa_documento, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdPutWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoIdPutWithHttpInfo($version, $company_id, $id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdPutRequest($version, $company_id, $id, $validation_override, $spesa_documento, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\SpesaDocumento' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 422:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 404:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 422:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdPutAsync
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdPutAsync($version, $company_id, $id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoIdPutAsyncWithHttpInfo($version, $company_id, $id, $validation_override, $spesa_documento, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoIdPutAsyncWithHttpInfo
     *
     * Aggiorna un elemento esistente.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoIdPutAsyncWithHttpInfo($version, $company_id, $id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento';
        $request = $this->apiVversionCompanyIdSpesaDocumentoIdPutRequest($version, $company_id, $id, $validation_override, $spesa_documento, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoIdPut'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  int $id ID univoco dell&#39;elemento da aggiornare. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoIdPutRequest($version, $company_id, $id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoIdPut'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoIdPut'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoIdPut'
            );
        }

        // verify the required parameter 'id' is set
        if ($id === null || (is_array($id) && count($id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $id when calling apiVversionCompanyIdSpesaDocumentoIdPut'
            );
        }




        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento/{id}';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $validation_override,
            'validationOverride', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }
        // path params
        if ($id !== null) {
            $resourcePath = str_replace(
                '{' . 'id' . '}',
                ObjectSerializer::toPathValue($id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($spesa_documento)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($spesa_documento));
            } else {
                $httpBody = $spesa_documento;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'PUT',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoNewGet
     *
     * Restituisce una nuova istanza di una spesa documento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoNewGet($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoNewGetWithHttpInfo($version, $company_id, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoNewGetWithHttpInfo
     *
     * Restituisce una nuova istanza di una spesa documento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoNewGetWithHttpInfo($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoNewGetRequest($version, $company_id, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 200:
                    if ('\Amica\Sdk\Model\SpesaDocumento' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoNewGetAsync
     *
     * Restituisce una nuova istanza di una spesa documento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoNewGetAsync($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoNewGetAsyncWithHttpInfo($version, $company_id, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoNewGetAsyncWithHttpInfo
     *
     * Restituisce una nuova istanza di una spesa documento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoNewGetAsyncWithHttpInfo($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento';
        $request = $this->apiVversionCompanyIdSpesaDocumentoNewGetRequest($version, $company_id, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoNewGet'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoNewGetRequest($version, $company_id, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoNewGet'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoNewGet'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoNewGet'
            );
        }


        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento/new';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'GET',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoPost
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails
     */
    public function apiVversionCompanyIdSpesaDocumentoPost($version, $company_id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'][0])
    {
        list($response) = $this->apiVversionCompanyIdSpesaDocumentoPostWithHttpInfo($version, $company_id, $validation_override, $spesa_documento, $contentType);
        return $response;
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoPostWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'] to see the possible values for this operation
     *
     * @throws \Amica\Sdk\ApiException on non-2xx response or if the response body is not in the expected format
     * @throws \InvalidArgumentException
     * @return array of \Amica\Sdk\Model\SpesaDocumento|\Amica\Sdk\Model\ProblemDetails|\Amica\Sdk\Model\ProblemDetails, HTTP status code, HTTP response headers (array of strings)
     */
    public function apiVversionCompanyIdSpesaDocumentoPostWithHttpInfo($version, $company_id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'][0])
    {
        $request = $this->apiVversionCompanyIdSpesaDocumentoPostRequest($version, $company_id, $validation_override, $spesa_documento, $contentType);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? (string) $e->getResponse()->getBody() : null
                );
            } catch (ConnectException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    (int) $e->getCode(),
                    null,
                    null
                );
            }

            $statusCode = $response->getStatusCode();


            switch($statusCode) {
                case 201:
                    if ('\Amica\Sdk\Model\SpesaDocumento' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\SpesaDocumento' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\SpesaDocumento', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 409:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                case 400:
                    if ('\Amica\Sdk\Model\ProblemDetails' === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ('\Amica\Sdk\Model\ProblemDetails' !== 'string') {
                            try {
                                $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                            } catch (\JsonException $exception) {
                                throw new ApiException(
                                    sprintf(
                                        'Error JSON decoding server response (%s)',
                                        $request->getUri()
                                    ),
                                    $statusCode,
                                    $response->getHeaders(),
                                    $content
                                );
                            }
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, '\Amica\Sdk\Model\ProblemDetails', []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
            }

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        (string) $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    (string) $response->getBody()
                );
            }

            $returnType = '\Amica\Sdk\Model\SpesaDocumento';
            if ($returnType === '\SplFileObject') {
                $content = $response->getBody(); //stream goes to serializer
            } else {
                $content = (string) $response->getBody();
                if ($returnType !== 'string') {
                    try {
                        $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
                    } catch (\JsonException $exception) {
                        throw new ApiException(
                            sprintf(
                                'Error JSON decoding server response (%s)',
                                $request->getUri()
                            ),
                            $statusCode,
                            $response->getHeaders(),
                            $content
                        );
                    }
                }
            }

            return [
                ObjectSerializer::deserialize($content, $returnType, []),
                $response->getStatusCode(),
                $response->getHeaders()
            ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\SpesaDocumento',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 409:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
                case 400:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\Amica\Sdk\Model\ProblemDetails',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoPostAsync
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoPostAsync($version, $company_id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'][0])
    {
        return $this->apiVversionCompanyIdSpesaDocumentoPostAsyncWithHttpInfo($version, $company_id, $validation_override, $spesa_documento, $contentType)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation apiVversionCompanyIdSpesaDocumentoPostAsyncWithHttpInfo
     *
     * Salva un nuovo elemento.
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function apiVversionCompanyIdSpesaDocumentoPostAsyncWithHttpInfo($version, $company_id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'][0])
    {
        $returnType = '\Amica\Sdk\Model\SpesaDocumento';
        $request = $this->apiVversionCompanyIdSpesaDocumentoPostRequest($version, $company_id, $validation_override, $spesa_documento, $contentType);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    if ($returnType === '\SplFileObject') {
                        $content = $response->getBody(); //stream goes to serializer
                    } else {
                        $content = (string) $response->getBody();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    return [
                        ObjectSerializer::deserialize($content, $returnType, []),
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];
                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        (string) $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'apiVversionCompanyIdSpesaDocumentoPost'
     *
     * @param  string $version Versione API. (required)
     * @param  string $company_id ID univoco dell&#39;azienda. (required)
     * @param  bool|null $validation_override Se applicare eventualmente l&#39;override della validazione. (optional)
     * @param  \Amica\Sdk\Model\SpesaDocumento|null $spesa_documento Istanza dell&#39;elemento da salvare. (optional)
     * @param  string $contentType The value for the Content-Type header. Check self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'] to see the possible values for this operation
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    public function apiVversionCompanyIdSpesaDocumentoPostRequest($version, $company_id, $validation_override = null, $spesa_documento = null, string $contentType = self::contentTypes['apiVversionCompanyIdSpesaDocumentoPost'][0])
    {

        // verify the required parameter 'version' is set
        if ($version === null || (is_array($version) && count($version) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $version when calling apiVversionCompanyIdSpesaDocumentoPost'
            );
        }

        // verify the required parameter 'company_id' is set
        if ($company_id === null || (is_array($company_id) && count($company_id) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $company_id when calling apiVversionCompanyIdSpesaDocumentoPost'
            );
        }




        $resourcePath = '/api/v{version}/{companyId}/SpesaDocumento';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;

        // query params
        $queryParams = array_merge($queryParams, ObjectSerializer::toQueryValue(
            $validation_override,
            'validationOverride', // param base name
            'boolean', // openApiType
            'form', // style
            true, // explode
            false // required
        ) ?? []);


        // path params
        if ($version !== null) {
            $resourcePath = str_replace(
                '{' . 'version' . '}',
                ObjectSerializer::toPathValue($version),
                $resourcePath
            );
        }
        // path params
        if ($company_id !== null) {
            $resourcePath = str_replace(
                '{' . 'companyId' . '}',
                ObjectSerializer::toPathValue($company_id),
                $resourcePath
            );
        }


        $headers = $this->headerSelector->selectHeaders(
            ['application/json', ],
            $contentType,
            $multipart
        );

        // for model (json/xml)
        if (isset($spesa_documento)) {
            if (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the body
                $httpBody = \GuzzleHttp\Utils::jsonEncode(ObjectSerializer::sanitizeForSerialization($spesa_documento));
            } else {
                $httpBody = $spesa_documento;
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $formParamValueItems = is_array($formParamValue) ? $formParamValue : [$formParamValue];
                    foreach ($formParamValueItems as $formParamValueItem) {
                        $multipartContents[] = [
                            'name' => $formParamName,
                            'contents' => $formParamValueItem
                        ];
                    }
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif (stripos($headers['Content-Type'], 'application/json') !== false) {
                # if Content-Type contains "application/json", json_encode the form parameters
                $httpBody = \GuzzleHttp\Utils::jsonEncode($formParams);
            } else {
                // for HTTP post (form)
                $httpBody = ObjectSerializer::buildQuery($formParams);
            }
        }

        // this endpoint requires Bearer (JWT) authentication (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }
        // this endpoint requires OAuth (access token)
        if (!empty($this->config->getAccessToken())) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $operationHost = $this->config->getHost();
        $query = ObjectSerializer::buildQuery($queryParams);
        return new Request(
            'POST',
            $operationHost . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
