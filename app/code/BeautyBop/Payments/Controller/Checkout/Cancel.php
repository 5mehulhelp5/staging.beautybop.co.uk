<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Controller\Checkout;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

class Cancel implements HttpGetActionInterface
{
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

    public function __construct(
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager,
        LoggerInterface $logger
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
        $this->logger = $logger;
    }

    /**
     * Handle PayPal cancellation.
     */
    public function execute()
    {
        $this->logger->info(
            'Customer cancelled PayPal checkout.'
        );

        $this->messageManager->addNoticeMessage(
            __('Your PayPal payment was cancelled.')
        );

        return $this->resultRedirectFactory
            ->create()
            ->setPath('checkout/cart');
    }
}