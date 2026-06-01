<?php

declare(strict_types=1);

/*
 * This file is part of the BringApi package.
 *
 * (c) Martin Madsen <crakter@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Crakter\BringApi\Clients;

use Crakter\BringApi\DefaultData\HttpMethods;
use Crakter\BringApi\DefaultData\Languages;
use Crakter\BringApi\DefaultData\ReturnFileContentTypes;
use Crakter\BringApi\DefaultData\ReturnFileTypes;
use Crakter\BringApi\Entity\ApiEntityInterface;
use Crakter\BringApi\Exception\BringClientException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use PhpOffice\PhpSpreadsheet\IOFactory as PhpSpreadsheetIOFactory;

/**
 * BringApi Base Client
 *
 * A facility for Client classes to be extended from.
 *
 * Quick setup: <code>class ReportsGenerateReport extends Base {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 * @deprecated since 4.0. Use the new Bring\Api facade and per-endpoint clients
 *             under the Bring\Api\Endpoint\* namespace. This class is kept for
 *             backwards-compatibility with v3 callers and will be removed in 5.0.
 */
abstract class Base
{
    /**
     * @var object $authorizationModule Implements the AuthorizationInterface
     */
    protected $authorizationModule;

    /**
     * @var ClientInterface PSR-7 / Guzzle HTTP client implementation
     */
    protected ClientInterface $client;

    /**
     * @var string $endPoint   The filetype to return
     */
    protected string $endPoint;

    /**
     * @var string $clientUrl    The clients url
     */
    protected string $clientUrl;
    /**
     * @var array $clientUrlVariables    The clients url Variables.
     */
    protected array $clientUrlVariables = [];

    /**
     * @var Response $response     The response
     */
    protected Response $response;

    /**
     * @var array $options     The options for Request
     */
    protected array $options = [];

    /**
     * @var string $httpMethod  The Method for HTTP
     */
    protected string $httpMethod;

    /**
     * @var ApiEntityInterface $apiEntity   Instance of ApiEntityInterface with all request body
     */
    protected ApiEntityInterface $apiEntity;

    /**
     * @var bool $isFullAddress   Set if URL is fixed and not parsed by UrlParameters
     */
    protected bool $isFullAddress = false;

    /**
     * @var string $alternativeAuthorizedUrl    Alternative url if we are logged in (required for tracking client)
     */
    protected string $alternativeAuthorizedUrl;

    /**
     * Set authorizationModule if provided and ClientInterface if provided, defaults to default GuzzleHttp Client
     * @return ClientsInterface       All clients must implement ClientsInterface
     */
    public function __construct(?ApiEntityInterface $apiEntity = null, ?AuthorizationInterface $authorizationModule = null, ?ClientInterface $client = null)
    {
        if ($authorizationModule instanceof AuthorizationInterface) {
            $this->setAuthorizationModule($authorizationModule);
        }
        if (!$client instanceof ClientInterface) {
            $client = new Client();
        }
        $this->client = $client;
        //Not all clients need ApiEntity.
        if ($apiEntity !== null) {
            $this->apiEntity = $apiEntity;
        }
    }

    /**
     * Set Client
     * @param  ClientInterface  $client client interface
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get Client
     * @return ClientInterface The underlying PSR-7 HTTP client
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Set Accept-Language
     * @param  string                        $value value of option
     * @throws InputValueNotAllowedException if value does not existing
     * @return ClientsInterface              All clients must implement ClientsInterface
     */
    public function setAcceptLanguage(string $value): self
    {
        $this->options['headers'] ??= [];
        $this->options['headers']['Accept-Language'] = Languages::get($value);

        return $this;
    }

    /**
     * Set header for request
     * @param  string           $option options name
     * @param  string           $value  value of option
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setHeader(string $option, string $value): self
    {
        $this->options['headers'] ??= [];
        $this->options['headers'][$option] = $value;

        return $this;
    }

    /**
     * Set query for request
     * @param  array            $value value of option
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setOptionsQuery(array $value): self
    {
        foreach ($value as &$var) {
            if (is_bool($var)) {
                $var = $var ? 'true' : 'false';
            }
        }
        unset($var);
        // Bring expects bare repeated keys (e.g. additional=A&additional=B) — strip the
        // numeric subscripts that http_build_query injects for indexed arrays.
        $this->options['query'] = preg_replace('/%5B(?:\d|[1-9]\d+)%5D=/', '=', http_build_query($value));

        return $this;
    }

    /**
     * Set json for request
     * @param  string           $value value of option
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setOptionsJson(array $value): self
    {
        $this->options['json'] = $value;

        return $this;
    }

    /**
     * Set options for request
     * @param  string           $value value of option
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setOptions(array $value): self
    {
        $this->options = $value;

        return $this;
    }

    /**
     * Gets Options to be sent with request
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Sets the Header to say we want XML back from Bring API
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReturnXml(): self
    {
        $this->setHeader('Accept', ReturnFileContentTypes::XML);
        $this->setHeader('Content-type', ReturnFileContentTypes::XML);
        $this->setEndPoint(ReturnFileTypes::XML);

        return $this;
    }

    /**
     * Sets the Header to say we want png back from Bring API
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReturnPng(): self
    {
        $this->setHeader('Accept', ReturnFileContentTypes::PNG);
        $this->setHeader('Content-type', ReturnFileContentTypes::PNG);
        $this->setEndPoint(ReturnFileTypes::PNG);

        return $this;
    }

    /**
     * Sets the Header to say we want HTML back from Bring API
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReturnHtml(): self
    {
        $this->setHeader('Accept', ReturnFileContentTypes::HTML);
        $this->setHeader('Content-type', ReturnFileContentTypes::HTML);
        $this->setEndPoint(ReturnFileTypes::HTML);

        return $this;
    }

    /**
     * Sets the Header to say we want XLS back from Bring API
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReturnXls(): self
    {
        $this->setHeader('Accept', ReturnFileContentTypes::XLS);
        $this->setHeader('Content-type', ReturnFileContentTypes::XLS);
        $this->setEndPoint(ReturnFileTypes::XLS);

        return $this;
    }

    /**
     * Sets the Header to say we want JSON back from Bring API
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setReturnJson(): self
    {
        $this->setHeader('Accept', ReturnFileContentTypes::JSON);
        $this->setHeader('Content-type', ReturnFileContentTypes::JSON);
        $this->setEndPoint(ReturnFileTypes::JSON);

        return $this;
    }

    /**
     * Sets the endpoint for request URL
     * @param  string           $endPoint The endpoint
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setEndPoint(string $endPoint): self
    {
        $this->endPoint = $endPoint;

        return $this;
    }

    /**
     * Gets the endpoint for request URL
     */
    public function getEndPoint(): string
    {
        return $this->endPoint ?? '';
    }

    /**
     * Sets the Client URL for request
     * @param  string           $clientUrl     Url to be sent with request
     * @param  bool             $isFullAddress Set if URL is full address - not parsed by UrlVariables
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setClientUrl(string $clientUrl, bool $isFullAddress = false): self
    {
        $this->clientUrl = $clientUrl;
        $this->setIsFullAddress($isFullAddress);

        return $this;
    }

    /**
     * Gets the Client URL for request
     */
    public function getClientUrl(): string
    {
        return $this->clientUrl;
    }

    /**
     * Sets the $isFullAddress variable so that we can supply with full address.
     * @param  bool             $isFullAddress Set if URL is full address - not parsed by UrlVariables
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setIsFullAddress(bool $isFullAddress): self
    {
        $this->isFullAddress = $isFullAddress;

        return $this;
    }

    /**
     * Gets the $isFullAddress variable
     * @return bool $isFullAddress  if URL is full address true/false - not parsed by UrlVariables
     */
    public function getIsFullAddress(): bool
    {
        return $this->isFullAddress;
    }

    /**
     * Sets the Variables for Client URL
     * @param  array            ...$args
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setClientUrlVariables(...$args): self
    {
        $this->clientUrlVariables = $args;

        return $this;
    }

    /**
     * Gets the Variables for Client URL, used in send function.
     */
    public function getClientUrlVariables(): array
    {
        return $this->clientUrlVariables;
    }

    /**
     * Set the response from request
     * @param  Response         $response PSR-7 complient Response Interface.
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * get the response from request
     * @return Response PSR-7 complient Response Interface.
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Returns the response in JSON format
     */
    public function toJson(): string
    {
        switch ($this->getEndPoint()) {
            case ReturnFileTypes::XML:
                $body = (string) $this->getResponse()->getBody();
                $previousErrors = libxml_use_internal_errors(true);
                try {
                    $xml = simplexml_load_string($body);
                } finally {
                    libxml_clear_errors();
                    libxml_use_internal_errors($previousErrors);
                }
                if ($xml === false) {
                    return $body;
                }

                return json_encode($xml);
            case ReturnFileTypes::XLS:
                $tmpFile = tempnam(sys_get_temp_dir(), 'BringApi') ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bringapi_' . bin2hex(random_bytes(8)) . '.xls';
                file_put_contents($tmpFile, (string) $this->getResponse()->getBody());
                if (!class_exists(PhpSpreadsheetIOFactory::class) && !class_exists(\PHPExcel_IOFactory::class)) {
                    @unlink($tmpFile);
                    throw new BringClientException('XLS response decoding requires phpoffice/phpspreadsheet (preferred) or the legacy phpoffice/phpexcel package to be installed.');
                }
                if (class_exists(PhpSpreadsheetIOFactory::class)) {
                    $spreadsheet = PhpSpreadsheetIOFactory::load($tmpFile);
                } else {
                    $spreadsheet = \PHPExcel_IOFactory::load($tmpFile);
                }
                @unlink($tmpFile);
                $array = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                $header = [];
                $rows = [];
                $rowIndex = 0;
                foreach ($array as $val) {
                    if ($rowIndex === 0) {
                        $header = $val;
                        $rowIndex++;
                        continue;
                    }
                    $obj = new \stdClass();
                    foreach ($val as $k => $v) {
                        if (isset($header[$k])) {
                            $obj->{$header[$k]} = $v;
                        }
                    }
                    $rows[] = $obj;
                    $rowIndex++;
                }

                return json_encode(count($rows) === 1 ? $rows[0] : $rows);
            case ReturnFileTypes::JSON:
            default:
                return (string) $this->getResponse()->getBody();
        }
    }

    /**
     * Check if string is compatible json string
     * @param  string $string JSON string
     * @return bool   true if correct json, false if not
     */
    public function isJson(string $string): bool
    {
        $array = json_decode($string, true);

        return (json_last_error() === JSON_ERROR_NONE) && is_array($array);
    }

    /**
     * Returns the response in Array format
     */
    public function toArray(): array|null
    {
        return json_decode($this->toJson(), true);
    }

    /**
     * toXml returnes the object at hand in form of an xml string
     * @param  string $xmlRoot Name of element root
     */
    public function toXml(string $xmlRoot): string
    {
        $xml = new \SimpleXMLElement("<{$xmlRoot}/>");
        $result = $this->toArray();
        $this->recursiveXml($xml, $result);

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return $dom->saveXML();
    }

    /**
     * Recursive XML function to loop through all values.
     */
    private function recursiveXml(\SimpleXMLElement $object, array $data): bool
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild((string) $key);
                $this->recursiveXml($new_object, $value);
            } else {
                $escaped = htmlspecialchars((string) ($value ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $object->addChild((string) $key, $escaped);
            }
        }

        return true;
    }

    /**
     * Sets the HTTP method to GET
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setGet(): self
    {
        $this->setHttpMethod(HttpMethods::GET);

        return $this;
    }

    /**
     * Sets the HTTP method to POST
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setPost(): self
    {
        $this->setHttpMethod(HttpMethods::POST);

        return $this;
    }

    /**
     * Sets the HTTP method
     * @param  string           $name HTTP method to be sent with request(POST, GET)
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setHttpMethod(string $name): self
    {
        $this->httpMethod = $name;

        return $this;
    }

    /**
     * Gets the HTTP method
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * Sets the AuthorizationModule
     * @param  AuthorizationModule $authorizationModule Authorization must implement AuthorizationInterface
     * @return ClientsInterface    All clients must implement ClientsInterface
     */
    public function setAuthorizationModule(AuthorizationInterface $authorizationModule): self
    {
        $this->authorizationModule = $authorizationModule;

        return $this;
    }

    /**
     * Gets the AuthorizationModule
     * @return AuthorizationInterface Authorization must implement AuthorizationInterface
     */
    public function getAuthorizationModule(): AuthorizationInterface
    {
        return $this->authorizationModule;
    }

    /**
     * Sets the AuthorizationModule
     * @param  ApiEntityInterface $apiEntity Entity must implement ApiEntityInterface
     * @return ClientsInterface   All clients must implement ClientsInterface
     */
    public function setApiEntity(ApiEntityInterface $apiEntity): self
    {
        $this->apiEntity = $apiEntity;

        return $this;
    }

    /**
     * Gets the ApiEntity
     * @return ApiEntityInterface Entity must implement ApiEntityInterface
     */
    public function getApiEntity(): ApiEntityInterface
    {
        return $this->apiEntity;
    }

    /**
     * Sets the AlternativeAuthorizedUrl
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function setAlternativeAuthorizedUrl(string $alternativeAuthorizedUrl): self
    {
        $this->alternativeAuthorizedUrl = $alternativeAuthorizedUrl;

        return $this;
    }

    /**
     * Gets the AlternativeAuthorizedUrl
     */
    public function getAlternativeAuthorizedUrl(): string
    {
        return $this->alternativeAuthorizedUrl ?? '';
    }

    /**
     * Sends the request to Bring API with processed information.
     * @see Base::setReturnJson()
     * @see Base::setClientUrl()
     * @see Base::getEndPoint()
     * @see Base::setHeader()
     * @see Base::getClientUrlVariables()
     * @see Base::getHttpMethod()
     * @see Base::getClientUrl()
     * @see Base::getOptions()
     * @see AuthorizationInterface::getClientUrl()
     * @see AuthorizationInterface::getClientId()
     * @see AuthorizationInterface::getApiKey()
     * @see AuthorizationInterface::hasAuthorization()
     * @see ClientInterface::request()
     * @see Response::getResponse()
     * @return ClientsInterface All clients must implement ClientsInterface
     */
    public function send()
    {
        // Defaults to Json return if not set.
        if ($this->getEndPoint() === '') {
            $this->setReturnJson();
        }
        if ($this->authorizationModule !== null) {
            if ($this->getIsFullAddress() === false && ($this->getAlternativeAuthorizedUrl() !== '' && $this->getAlternativeAuthorizedUrl() !== '0')) {
                $this->setClientUrl($this->getAlternativeAuthorizedUrl());
            }
            if ($this->authorizationModule->has('clientUrl')) {
                $this->setHeader('X-Bring-Client-URL', $this->authorizationModule->getClientUrl());
            }
            if ($this->authorizationModule->hasAuthorization()) {
                // Canonical header casing per Bring docs (https://developer.bring.com/api/authentication/).
                $this->setHeader('X-Mybring-API-Uid', $this->authorizationModule->getClientId());
                $this->setHeader('X-Mybring-API-Key', $this->authorizationModule->getApiKey());
            }
        }
        if ($this->getIsFullAddress() === false) {
            if ($this->getClientUrlVariables() === []) {
                $this->processClientUrlVariables();
            }
            if ($this->getClientUrlVariables() !== []) {
                $this->setClientUrl(vsprintf($this->getClientUrl(), $this->getClientUrlVariables()));
            }
        }
        $this->processEntity();
        try {
            $request = $this->client->request(
                $this->getHttpMethod(),
                $this->getClientUrl(),
                $this->getOptions(),
            );
        } catch (ClientException $e) {
            $status = $e->getResponse()?->getStatusCode() ?? 0;
            throw new BringClientException(
                sprintf('Bring API HTTP %d error from %s.', $status, static::class),
                0,
                $e,
            );
        } catch (RequestException $e) {
            throw new BringClientException(
                sprintf('Bring API request failed from %s: %s', static::class, $this->redactCredentials($e->getMessage())),
                0,
                $e,
            );
        }
        $this->setResponse($request);

        return $this;
    }

    /**
     * Mask the API key in any string that may contain it (used to scrub Guzzle's
     * verbose exception messages, which echo back full request headers).
     */
    protected function redactCredentials(string $message): string
    {
        if ($this->authorizationModule === null) {
            return $message;
        }
        try {
            $key = $this->authorizationModule->getApiKey();
            if ($key !== '') {
                $message = str_replace($key, '***redacted***', $message);
            }
        } catch (\Throwable) {
            // No key set — nothing to redact.
        }

        return $message;
    }
}
