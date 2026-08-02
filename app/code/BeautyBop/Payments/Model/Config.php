<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED =
        'beautybop_payments/general/enabled';

    private const XML_PATH_SANDBOX =
        'beautybop_payments/general/sandbox';

    private const XML_PATH_CLIENT_ID =
        'beautybop_payments/paypal/client_id';

    private const XML_PATH_SECRET =
        'beautybop_payments/paypal/secret';

    /** @var ScopeConfigInterface; */
    private $scopeConfig;


    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * Check if payments module enabled.
     */
    public function isEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Check sandbox mode.
     */
    public function isSandbox(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_SANDBOX,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Get PayPal Client ID.
     */
    public function getClientId(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_ID,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Get PayPal Secret.
     */
    public function getSecret(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SECRET,
            ScopeInterface::SCOPE_STORE
        );
    }
}