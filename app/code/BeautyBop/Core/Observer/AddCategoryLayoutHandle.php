<?php

declare(strict_types=1);

namespace BeautyBop\Core\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class AddCategoryLayoutHandle implements ObserverInterface
{
    private Registry $registry;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
    }

    public function execute(Observer $observer): void
    {
        if ($observer->getData('full_action_name') !== 'catalog_category_view') {
            return;
        }

        $category = $this->registry->registry('current_category');

        if (!$category || !$category->getId()) {
            return;
        }

        $urlKey = (string) $category->getUrlKey();

        if ($urlKey === '') {
            return;
        }

        $safeUrlKey = strtolower($urlKey);
        $safeUrlKey = preg_replace('/[^a-z0-9]+/', '_', $safeUrlKey);
        $safeUrlKey = trim((string) $safeUrlKey, '_');

        if ($safeUrlKey === '') {
            return;
        }

        $layout = $observer->getData('layout');

        if (!$layout) {
            return;
        }

        $layout->getUpdate()->addHandle(
            'beautybop_category_' . $safeUrlKey
        );
    }
}
