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

/**
 * BringApi AuthorizationInterface
 *
 * A Interface to be implemented by a Authorization class
 *
 * Quick setup: <code>class Authorization implements AuthorizationInterface {}</code>
 *
 * @author Martin Madsen <crakter@gmail.com>
 */
interface AuthorizationInterface
{
    /**
     * Set X-MyBring-API-Key in header request
     * @param string $apiKey Your Mybring APIkey
     * @example 1234abc-abcd-1234-5678-abcd1234abcd
     * @return object AuthorizationInterface
     */
    public function setApiKey(string $apiKey): AuthorizationInterface;

    /**
     * Set X-MyBring-API-Uid in header request
     * @param string $clientId Your Mybring login
     * @example john.doe@example.com
     * @return object AuthorizationInterface
     */
    public function setClientId(string $clientId): AuthorizationInterface;

    /**
     * Set X-Bring-Client-URL in header request
     * @param string $clientUrl Your Url
     * @example https://example.com/
     * @return object AuthorizationInterface
     */
    public function setClientUrl(string $clientUrl): AuthorizationInterface;

    /**
     * Check if Authorization is set
     * @return bool true/false
     */
    public function hasAuthorization(): bool;

    /**
     * Check if variable is set
     * @return bool   true/false
     */
    public function has(string $variable): bool;

    /**
     * Gets the variable
     * @throws ValueNotSetException if "has" fails.
     */
    public function get(string $name): string;

    /**
     * Gets the variable
     * @throws ValueNotSetException if "has" fails.
     */
    public function getApiKey(): string;

    /**
     * Gets the variable
     * @throws ValueNotSetException if "has" fails.
     */
    public function getClientId(): string;

    /**
     * Gets the variable
     * @throws ValueNotSetException if "has" fails.
     */
    public function getClientUrl(): string;
}
