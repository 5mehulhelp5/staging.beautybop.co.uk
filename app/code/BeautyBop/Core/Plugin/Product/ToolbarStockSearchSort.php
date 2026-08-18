<?php

declare(strict_types=1);

namespace BeautyBop\Core\Plugin\Product;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as FulltextCollection;

class ToolbarStockSearchSort
{
    public function beforeSetCollection(
        Toolbar $subject,
        $collection
    ): array {
        if ($collection instanceof FulltextCollection) {
            /*
             * BeautyBop OpenSearch index:
             *
             * is_out_of_stock = 1 → in stock
             * is_out_of_stock = 0 → out of stock
             *
             * Therefore DESC puts available products first.
             */
            $collection->setOrder('is_out_of_stock', 'DESC');
        }

        return [$collection];
    }
}