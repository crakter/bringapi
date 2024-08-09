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
 * BringApi Authorization
 *
 * A Class to facility for the Authorization for Bring Api
 *
 * Quick setup: <code>$auth = new Authorization('1234abc-abcd-1234-5678-abcd1234abcd',
 *                                              'john.doe@example.com', 'https://example.com/');</code>
 *
 * @implements AuthorizationInterface
 * @author Martin Madsen <crakter@gmail.com>
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
    public function __construct(string $apiKey = null, string $clientId = null, string $clientUrl = null)
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setApiKey(string $apiKey): AuthorizationInterface
    {
        $this->apiKey = $apiKey;

        return $this;
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
