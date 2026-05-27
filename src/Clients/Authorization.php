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

use Crakter\BringApi\Exception\ValueNotSetException;

/**
 * BringApi Authorization
 *
 * A Class to facility for the Authorization for Bring Api
 *
 * Quick setup: <code>$auth = new Authorization('1234abc-abcd-1234-5678-abcd1234abcd',
 *                                              'john.doe@example.com', 'https://example.com/');</code>
 *
 * @implements AuthorizationInterface
 * @author Martin Madsen <crakter@gmail.com>
 * @deprecated since 4.0. Use Bring\Api\Auth\Credentials together with
 *             Bring\Api\Auth\HeaderAuthorization.
 */
class Authorization implements AuthorizationInterface
{
    /**
     * @var string Holds the ApiKey
     */
    private string $apiKey;

    /**
     * @var string Holds the ClientId
     */
    private string $clientId;

    /**
     * @var string Holds the ClientUrl
     */
    private string $clientUrl;

    /**
     * Sets the parameters if they are supplied, or use fluent interface to do the same thing
     * @param  string                 $apiKey    Your Mybring APIkey
     * @param  string                 $clientId  Your Mybring login
     * @param  string                 $clientUrl Your Url
     * @return AuthorizationInterface
     */
    public function __construct(#[\SensitiveParameter] ?string $apiKey = null, ?string $clientId = null, ?string $clientUrl = null)
    {
        if ($apiKey !== null) {
            $this->setApiKey($apiKey);
        }
        if ($clientId !== null) {
            $this->setClientId($clientId);
        }
        if ($clientUrl !== null) {
            $this->setClientUrl($clientUrl);
        }
    }

    public function setApiKey(#[\SensitiveParameter] string $apiKey): AuthorizationInterface
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function __debugInfo(): array
    {
        $fingerprint = isset($this->apiKey) && $this->apiKey !== ''
            ? substr(hash('sha256', $this->apiKey), 0, 8)
            : null;

        return [
            'clientId' => $this->clientId ?? null,
            'clientUrl' => $this->clientUrl ?? null,
            'apiKeyFingerprint' => $fingerprint,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setClientId(string $clientId): AuthorizationInterface
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setClientUrl(string $clientUrl): AuthorizationInterface
    {
        $this->clientUrl = $clientUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAuthorization(): bool
    {
        return ($this->has('apiKey')) && ($this->has('clientId'));
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        return isset($this->{$name}) && !empty($this->{$name});
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): string
    {
        if (!$this->has($name)) {
            throw new ValueNotSetException(sprintf('Value %s is not set in %s.', $name, self::class));
        }

        return $this->{$name};
    }

    /**
     * {@inheritdoc}
     */
    public function getApiKey(): string
    {
        return $this->get('apiKey');
    }

    /**
     * {@inheritdoc}
     */
    public function getClientId(): string
    {
        return $this->get('clientId');
    }

    /**
     * {@inheritdoc}
     */
    public function getClientUrl(): string
    {
        return $this->get('clientUrl');
    }
}
