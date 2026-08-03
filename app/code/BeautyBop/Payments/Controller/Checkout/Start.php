<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Controller\Checkout;

use BeautyBop\Payments\Service\QuoteToCreateOrderRequest;
use Magento\Checkout\Model\Session;
use BeautyBop\Payments\Service\UrlProvider;
use BeautyBop\Payments\Service\PayPalOrderCreator;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use BeautyBop\Payments\Model\CreateOrderRequest;

class Start implements HttpGetActionInterface
{
    /**
     * @var PayPalOrderCreator
     */
    private PayPalOrderCreator $orderCreator;

    /**
     * @var RedirectFactory
     */
    private RedirectFactory $resultRedirectFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var UrlProvider
     */
    private UrlProvider $urlProvider;

    /**
     * @var QuoteToCreateOrderRequest
     */
    private QuoteToCreateOrderRequest $requestBuilder;

    /**
     * @var Session
     */
    private Session $checkoutSession;

    public function __construct(
        PayPalOrderCreator $orderCreator,
        RedirectFactory $resultRedirectFactory,
        UrlProvider $urlProvider,
        QuoteToCreateOrderRequest $requestBuilder,
        Session $checkoutSession,
        LoggerInterface $logger
    ) {
        $this->orderCreator = $orderCreator;
        $this->urlProvider = $urlProvider;
        $this->requestBuilder = $requestBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->logger = $logger;
    }

    /**
     * Redirect customer to PayPal.
     *
     * @throws LocalizedException
     */
    public function execute()
    {
        try {

            /*
             * Create a PayPal Sandbox Order.
             */
            $quote = $this->checkoutSession->getQuote();

            $this->logger->info(
                'Quote Totals',
                [
                    'subtotal' => $quote->getSubtotal(),
                    'shipping' => $quote->getShippingAddress()->getShippingAmount(),
                    'tax' => $quote->getShippingAddress()->getTaxAmount(),
                    'grand_total' => $quote->getGrandTotal()
                ]
            );

            $request = $this->requestBuilder->build(
                $quote
            );

            $order = $this->orderCreator->create($request);

            /*
             * Redirect customer to PayPal.
             */
            return $this->resultRedirectFactory
                ->create()
                ->setUrl(
                    $order->getApproveUrl()
                );

        } catch (\Throwable $exception) {

            $this->logger->critical($exception);

            throw new LocalizedException(
                __('Unable to start PayPal Checkout.')
            );
        }
    }
}