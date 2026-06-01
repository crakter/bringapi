<?php

declare(strict_types=1);

namespace Bring\Api\Auth;

use Psr\Http\Message\RequestInterface;

/**
 * Pass-through for endpoints that don't require authentication (e.g. some
 * Shipping Guide and public Postal Code lookups).
 */
final class NullAuthorization implements AuthorizationInterface
{
    #[\Override]
    public function isAuthenticated(): bool
    {
        return false;
    }

    #[\Override]
    public function applyTo(RequestInterface $request): RequestInterface
    {
        return $request;
    }
}
