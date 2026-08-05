<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class CreateOrderRequest
{
    /**
     * @var string
     */
    private string $currency;

    /**
     * @var float
     */
    private float $amount;

    /**
     * @var string
     */
    private string $returnUrl;

    /**
     * @var string
     */
    private string $cancelUrl;

    /**
     * CreateOrderRequest constructor.
     */
    public function __construct(
        string $currency,
        float $amount,
        string $returnUrl,
        string $cancelUrl
    ) {
        $this->currency = $currency;
        $this->amount = $amount;
        $this->returnUrl = $returnUrl;
        $this->cancelUrl = $cancelUrl;
    }

    /**
     * Get currency.
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * Get amount.
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Get return URL.
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    /**
     * Get cancel URL.
     */
    public function getCancelUrl(): string
    {
        return $this->cancelUrl;
    }

    /**
     * Get the PayPal webhook URL.
     */
    public function getWebhookUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'beautybop_payments/webhook/index'
        );
    }
}