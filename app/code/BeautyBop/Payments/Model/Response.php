<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class Response
{
    /**
     * @var int
     */
    private int $statusCode;

    /**
     * @var string
     */
    private string $body;

    /**
     * @var array
     */
    private array $headers;

    /**
     * Response constructor.
     *
     * @param int $statusCode
     * @param string $body
     * @param array $headers
     */
    public function __construct(
        int $statusCode,
        string $body,
        array $headers = []
    ) {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }

    /**
     * Get HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get raw response body.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Decode JSON response.
     */
    public function getJson(): array
    {
        $decoded = json_decode($this->body, true);

        return is_array($decoded)
            ? $decoded
            : [];
    }

    /**
     * Get response headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Was the request successful?
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200
            && $this->statusCode < 300;
    }

    /**
     * Was the request a client error?
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400
            && $this->statusCode < 500;
    }

    /**
     * Was the request a server error?
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500;
    }
}