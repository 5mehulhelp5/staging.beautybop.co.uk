<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Controller\Checkout;

use BeautyBop\Payments\Service\PayPalCaptureService;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\LocalizedException;

class Callback implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var RedirectFactory
     */
    private RedirectFactory $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var PayPalCaptureService
     */
    private PayPalCaptureService $captureService;

    public function __construct(
        RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager,
        LoggerInterface $logger,
        PayPalCaptureService $captureService
    ) {
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->captureService = $captureService;
    }

    /**
     * Handle PayPal return.
     */
    public function execute()
    {
        $token = (string)$this->request->getParam('token');
        $payerId = (string)$this->request->getParam('PayerID');

        if ($token === '') {
            throw new LocalizedException(
                __('Missing PayPal order token.')
            );
        }

        $this->logger->info(
            'PayPal callback received.',
            [
                'token' => $token,
                'payer_id' => $payerId
            ]
        );

        $result = $this->captureService->capture($token);

        $this->logger->info(
            'PayPal payment captured.',
            [
                'order_id' => $result->getOrderId(),
                'capture_id' => $result->getCaptureId(),
                'status' => $result->getStatus(),
                'payer_email' => $result->getPayerEmail(),
                'payer_id' => $payerId
            ]
        );

        $this->messageManager->addSuccessMessage(
            __('Payment completed successfully.')
        );

        return $this->resultRedirectFactory
            ->create()
            ->setPath('checkout/cart');
    }
}