<?php

declare(strict_types=1);

namespace Bring\Api\Auth;

use Bring\Api\Http\HeaderNames;
use Psr\Http\Message\RequestInterface;

final class HeaderAuthorization implements AuthorizationInterface
{
    public function __construct(private readonly Credentials $credentials)
    {
    }

    #[\Override]
    public function isAuthenticated(): bool
    {
        return true;
    }

    #[\Override]
    public function applyTo(RequestInterface $request): RequestInterface
    {
        $request = $request
            ->withHeader(HeaderNames::AUTH_UID, $this->credentials->getUid())
            ->withHeader(HeaderNames::AUTH_KEY, $this->credentials->getApiKey());

        $clientUrl = $this->credentials->getClientUrl();
        if ($clientUrl !== null && $clientUrl !== '') {
            $request = $request->withHeader(HeaderNames::CLIENT_URL, $clientUrl);
        }

        return $request;
    }
}
