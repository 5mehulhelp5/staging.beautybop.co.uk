<?php

namespace BeautyBop\Core\Block\Product\View;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;

class Stock extends Template
{
    private Registry $registry;
    private GetProductSalableQtyInterface $getProductSalableQty;
    private StockResolverInterface $stockResolver;

    public function __construct(
        Context $context,
        Registry $registry,
        GetProductSalableQtyInterface $getProductSalableQty,
        StockResolverInterface $stockResolver,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockResolver = $stockResolver;

        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getSalableQty(): float
    {
        $product = $this->getProduct();

        if (!$product || !$product->getSku()) {
            return 0;
        }

        try {
            $stock = $this->stockResolver->execute(
                SalesChannelInterface::TYPE_WEBSITE,
                $this->_storeManager->getWebsite()->getCode()
            );

            return max(
                0,
                $this->getProductSalableQty->execute(
                    $product->getSku(),
                    $stock->getStockId()
                )
            );
        } catch (\Exception $e) {
            return 0;
        }
    }
}