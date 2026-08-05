<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Gateway;

use BeautyBop\Payments\Api\PaymentGatewayInterface;
use BeautyBop\Payments\Model\Config;
use Psr\Log\LoggerInterface;

abstract class AbstractGateway implements PaymentGatewayInterface
{
    /**
     * @var Config
     */
    protected Config $config;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;


    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }


    /**
     * Log payment activity.
     */
    protected function log(
        string $message,
        array $context = []
    ): void {

        $this->logger->info(
            $message,
            $context
        );
    }


    /**
     * Check if gateway is enabled.
     */
    protected function isEnabled(): bool
    {
        return $this->config->isEnabled();
    }


    /**
     * Check sandbox mode.
     */
    protected function isSandbox(): bool
    {
        return $this->config->isSandbox();
    }
}