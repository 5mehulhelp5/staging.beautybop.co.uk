<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class Credentials
{
    /**
     * @var string
     */
    private string $clientId;

    /**
     * @var string
     */
    private string $secret;

    /**
     * @var bool
     */
    private bool $sandbox;

    public function __construct(
        string $clientId,
        string $secret,
        bool $sandbox = true
    ) {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->sandbox = $sandbox;
    }

    /**
     * Get Client ID.
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * Get Secret.
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * Is Sandbox Mode?
     */
    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Get API base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }
}