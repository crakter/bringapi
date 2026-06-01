<?php

declare(strict_types=1);

namespace Bring\Api\Endpoint\Reports;

use Bring\Api\Endpoint\Endpoint;
use Bring\Api\Enum\ReportFormat;
use Bring\Api\Http\AcceptType;
use Bring\Api\Http\HttpMethod;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * GET https://www.mybring.com/reports/api/report/{reportId}.{format} (raw body)
 *
 * @implements Endpoint<string>
 */
final class DownloadEndpoint implements Endpoint
{
    public function __construct(
        private readonly string $reportId,
        private readonly ReportFormat $format = ReportFormat::JSON,
    ) {
    }

    #[\Override]
    public function method(): HttpMethod
    {
        return HttpMethod::GET;
    }

    #[\Override]
    public function accept(): AcceptType
    {
        return match ($this->format) {
            ReportFormat::JSON => AcceptType::JSON,
            ReportFormat::XML => AcceptType::XML,
            ReportFormat::XLS => AcceptType::XLS,
            ReportFormat::HTML => AcceptType::HTML,
        };
    }

    #[\Override]
    public function uri(UriFactoryInterface $uris): UriInterface
    {
        return $uris->createUri(sprintf(
            'https://www.mybring.com/reports/api/report/%s.%s',
            rawurlencode($this->reportId),
            $this->format->value,
        ));
    }

    #[\Override]
    public function buildRequest(
        RequestFactoryInterface $requests,
        StreamFactoryInterface $streams,
        UriFactoryInterface $uris,
    ): RequestInterface {
        return $requests
            ->createRequest($this->method()->value, $this->uri($uris))
            ->withHeader('Accept', $this->accept()->value);
    }

    #[\Override]
    public function parseResponse(ResponseInterface $response): string
    {
        return (string) $response->getBody();
    }
}
