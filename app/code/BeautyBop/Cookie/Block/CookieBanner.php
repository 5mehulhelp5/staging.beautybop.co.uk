<?php

namespace BeautyBop\Cookie\Block;

use Magento\Framework\View\Element\Template;

class CookieBanner extends Template
{
    /**
     * Should the banner display?
     */
    public function shouldShow(): bool
    {
        return true;
    }
}