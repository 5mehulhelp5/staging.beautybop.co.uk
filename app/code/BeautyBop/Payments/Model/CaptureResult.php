<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class CaptureResult
{
    /**
     * @var string
     */
    private string $orderId;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $captureId;

    /**
     * @var string
     */
    private string $payerEmail;

    public function __construct(
        string $orderId,
        string $status,
        string $captureId = '',
        string $payerEmail = ''
    ) {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->captureId = $captureId;
        $this->payerEmail = $payerEmail;
    }

    /**
     * Get PayPal Order ID.
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * Get capture status.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get PayPal Capture ID.
     */
    public function getCaptureId(): string
    {
        return $this->captureId;
    }

    /**
     * Get payer email.
     */
    public function getPayerEmail(): string
    {
        return $this->payerEmail;
    }
}