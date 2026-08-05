<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use Magento\Framework\UrlInterface;

class UrlProvider
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get the PayPal return URL.
     */
    public function getReturnUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'beautybop_payments/checkout/callback'
        );
    }

    /**
     * Get the PayPal cancel URL.
     */
    public function getCancelUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'beautybop_payments/checkout/cancel'
        );
    }

    /**
     * Get the PayPal checkout start URL.
     */
    public function getStartUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'beautybop_payments/checkout/start'
        );
    }
}