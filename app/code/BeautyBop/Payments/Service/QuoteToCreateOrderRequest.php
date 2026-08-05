<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use BeautyBop\Payments\Model\CreateOrderRequest;
use Magento\Quote\Model\Quote;

class QuoteToCreateOrderRequest
{
    /**
     * @var UrlProvider
     */
    private UrlProvider $urlProvider;

    public function __construct(
        UrlProvider $urlProvider
    ) {
        $this->urlProvider = $urlProvider;
    }

    /**
     * Build a PayPal CreateOrderRequest
     * from a Magento Quote.
     */
    public function build(
        Quote $quote
    ): CreateOrderRequest {

        return new CreateOrderRequest(
            $quote->getQuoteCurrencyCode(),
            (float)$quote->getGrandTotal(),
            $this->urlProvider->getReturnUrl(),
            $this->urlProvider->getCancelUrl()
        );
    }
}