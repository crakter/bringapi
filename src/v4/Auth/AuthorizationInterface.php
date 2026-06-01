<?php

declare(strict_types=1);

namespace Bring\Api\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * Decorates an outgoing PSR-7 request with Bring's authentication headers.
 */
interface AuthorizationInterface
{
    public function isAuthenticated(): bool;

    /**
     * Return a new request with auth headers applied. PSR-7 requests are
     * immutable, so the original $request is not mutated.
     */
    public function applyTo(RequestInterface $request): RequestInterface;
}
