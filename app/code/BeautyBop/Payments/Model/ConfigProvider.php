<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Config
     */
    private Config $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Provide checkout configuration.
     */
    public function getConfig(): array
    {
        if (!$this->config->isEnabled()) {
            return [];
        }

        return [
            'payment' => [
                PaymentMethod::CODE => [
                    'title' => $this->config->getTitle(),
                    'sandbox' => $this->config->isSandbox()
                ]
            ]
        ];
    }
}