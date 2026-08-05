<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Service;

use BeautyBop\Payments\Model\Config;
use BeautyBop\Payments\Model\Credentials;

class CredentialsProvider
{
    /**
     * @var Config
     */
    private Config $config;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Return the configured credentials.
     *
     * Later these will come from Magento Admin
     * (Stores → Configuration → BeautyBop → Payments).
     */
    public function get(): Credentials
    {
        return new Credentials(
            'AVwAGhKsYTOd9M5CBwH0Xtea9apqaiy8JNVDigf89vOZd7WOF9P_uTqS_L2xOKqi5ArtvYLBw10JV4DC',
            'EJzWPDl80O7Necl8jqw2oYT1qcmWMDK3eymwoh80BPbsLCFxfsE6l6xcxNXcJmAGWR7kL2BGxqMy0H4W',
            true
        );
    }
}