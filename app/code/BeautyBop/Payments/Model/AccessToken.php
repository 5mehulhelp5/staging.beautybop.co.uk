<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class AccessToken
{
    /**
     * OAuth access token.
     *
     * @var string
     */
    private string $token;

    /**
     * Token type.
     *
     * @var string
     */
    private string $type;

    /**
     * Lifetime in seconds.
     *
     * @var int
     */
    private int $expiresIn;

    /**
     * Time the token was created.
     *
     * @var int
     */
    private int $createdAt;

    public function __construct(
        string $token,
        string $type,
        int $expiresIn
    ) {
        $this->token = $token;
        $this->type = $type;
        $this->expiresIn = $expiresIn;
        $this->createdAt = time();
    }

    /**
     * Get access token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get token type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get token lifetime.
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Has the token expired?
     */
    public function isExpired(): bool
    {
        return time() >= ($this->createdAt + $this->expiresIn);
    }

    /**
     * Get expiry timestamp.
     */
    public function getExpiresAt(): int
    {
        return $this->createdAt + $this->expiresIn;
    }
}