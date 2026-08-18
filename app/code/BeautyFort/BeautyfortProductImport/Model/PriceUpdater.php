<?php
declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use BeautyFort\BeautyfortProductImport\Helper\Api;
use BeautyFort\BeautyfortProductImport\Helper\Price;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use BeautyFort\BeautyfortProductImport\Logger\Logger;

use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class PriceUpdater
{
    /** @var CollectionFactory */
    private $productCollectionFactory;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var Api */
    private $api;

    /** @var Price */
    private $price;

    /** @var Logger */
    private $logger;

    /** @var State */
    private $appState;

    /** @var StockRegistryInterface */
    private $stockRegistry;

    public function __construct(
        CollectionFactory $productCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Api $api,
        Price $price,
        StockRegistryInterface $stockRegistry,
        Logger $logger,
        State $appState
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->api = $api;
        $this->price = $price;
        $this->stockRegistry = $stockRegistry;
        $this->logger = $logger;
        $this->appState = $appState;
    }

    public function execute(): void
    {

        try {
             $this->appState->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // Ignore if already set
        }
        
        $this->logger->info('🕒 PRICE CRON START');

        $supplierProducts = $this->api->getStockFile();

        $this->logger->info('Supplier stock file downloaded', [
            'count' => count($supplierProducts)
        ]);

        if (empty($supplierProducts)) {
            $this->logger->error('No supplier products returned. Aborting price update.');
            return;
        }

        $supplierLookup = [];

        foreach ($supplierProducts as $item) {

            if (empty($item['StockCode'])) {
                continue;
            }

            $stockCode = strtoupper(trim((string)$item['StockCode']));

            $supplierLookup[$stockCode] = $item;
        }

        $this->logger->info('Supplier lookup built', [
            'count' => count($supplierLookup)
        ]);

        $this->logger->info('First supplier lookup keys', [
            'keys' => array_slice(array_keys($supplierLookup), 0, 20)
        ]);

       

        $updatedCount = 0;
        $checkedCount = 0;
        $unchangedCount = 0;
        $errorCount = 0;
        $stockUpdatedCount = 0;
   

        /**
         * 2️⃣ Load Magento Beautyfort products
         */
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect(['sku', 'price', 'beautyfort_source']);
        $collection->addAttributeToFilter('beautyfort_source', 1);

        $this->logger->info('Magento BeautyFort products loaded', [
            'count' => $collection->getSize()
        ]);

        /**
         * 3️⃣ Loop Magento products and match by SKU in memory
         */
        foreach ($collection as $product) {

            try {
                
                $sku = trim((string)$product->getSku());
                $lookupSku = strtoupper($sku);

                /*if($sku !== 'E555202'){
                    continue;
                }*/

                
                if (!isset($supplierLookup[$lookupSku])) {

                    $checkedCount++;

                    $this->logger->warning(
                        'Supplier SKU not found - setting product out of stock',
                        ['sku' => $sku]
                    );

                    try {

                        $stockItem = $this->stockRegistry->getStockItemBySku($sku);

                        $currentQty = (float)$stockItem->getQty();
                        $currentIsInStock = (bool)$stockItem->getIsInStock();

                        /*
                        * BeautyFort manages this product but the SKU is not
                        * present in the current supplier catalogue.
                        *
                        * Keep the Magento product itself enabled and retain
                        * price/content/URL, but make it unavailable to purchase.
                        */
                        if ($currentQty != 0 || $currentIsInStock) {

                            $stockItem->setQty(0);
                            $stockItem->setIsInStock(false);

                            $this->stockRegistry->updateStockItemBySku(
                                $sku,
                                $stockItem
                            );

                            $stockUpdatedCount++;
                            $updatedCount++;

                            $this->logger->info(
                                'Missing supplier SKU set out of stock',
                                [
                                    'sku' => $sku,
                                    'old_qty' => $currentQty,
                                    'new_qty' => 0,
                                    'old_is_in_stock' => $currentIsInStock,
                                    'new_is_in_stock' => false
                                ]
                            );

                        } else {

                            $unchangedCount++;

                            $this->logger->info(
                                'Missing supplier SKU already out of stock',
                                ['sku' => $sku]
                            );
                        }

                    } catch (\Throwable $e) {

                        $errorCount++;

                        $this->logger->error(
                            'Failed setting missing supplier SKU out of stock',
                            [
                                'sku' => $sku,
                                'message' => $e->getMessage()
                            ]
                        );
                    }

                    continue;
                }

   

                $supplierData = $supplierLookup[$lookupSku];

                $stockItem = $this->stockRegistry->getStockItemBySku($sku);

                $currentQty = (int)$stockItem->getQty();

                $newQty = (int)($supplierData['StockLevel'] ?? 0);

                $currentIsInStock = (bool)$stockItem->getIsInStock();
                $newIsInStock = $newQty > 0;

                $this->logger->info('Stock comparison', [
                    'sku' => $sku,
                    'current_qty' => $currentQty,
                    'new_qty' => $newQty
                ]);


                $this->logger->info('Supplier lookup hit', [
                    'sku'   => $sku,
                    'price' => $supplierData['Price'] ?? null,
                    'rrp'   => $supplierData['RRP'] ?? null,
                    'stock' => $supplierData['StockLevel'] ?? null,
                ]);

                $checkedCount++;

            
                $oldPrice = (float)$product->getPrice();
                $supplierCost = (float)($supplierData['Price'] ?? 0);

                $newPrice = $this->price->calculatePrice($supplierCost);

                $currentRrp = (float) $product->getData('beautyfort_rrp');
                $newRrp = (float) ($supplierData['RRP'] ?? 0);

                $this->logger->info('RRP comparison', [
                    'sku'         => $sku,
                    'current_rrp' => $currentRrp,
                    'new_rrp'     => $newRrp
                ]);

                $hasChanges = false;

                if ($currentRrp != $newRrp) {

                    $product->setData('beautyfort_rrp', $newRrp);

                    $hasChanges = true;

                    $this->logger->info('RRP changed', [
                        'sku' => $sku,
                        'old' => $currentRrp,
                        'new' => $newRrp
                    ]);
                }

                $this->logger->info('Price comparison', [
                    'sku' => $sku,
                    'old_price' => $oldPrice,
                    'new_price' => $newPrice
                ]);

                if ($newPrice != $oldPrice) {

                    $product->setPrice($newPrice);

                    $hasChanges = true;

                    $this->logger->info('Price changed', [
                        'sku' => $sku,
                        'old_price' => $oldPrice,
                        'new_price' => $newPrice
                    ]);
                } 


                if ( $currentQty != $newQty || $currentIsInStock != $newIsInStock) {
                    $hasChanges = true;
                }

                if ($hasChanges) {

                        try {

                            $this->logger->info('Saving product', ['sku' => $sku]);

                            $product = $this->productRepository->get($sku);

                            $product->setPrice($newPrice);

                            $product->setData('beautyfort_rrp', $newRrp);

                            $this->productRepository->save($product);

                            // update stock if different
                            if ( $currentQty != $newQty || $currentIsInStock != $newIsInStock ){

                                $stockItem->setQty($newQty);
                                $stockItem->setIsInStock($newIsInStock);

                                $this->stockRegistry->updateStockItemBySku(
                                    $sku,
                                    $stockItem
                                );

                                $this->logger->info('Stock changed', [
                                    'sku' => $sku,
                                    'old_qty' => $currentQty,
                                    'new_qty' => $newQty
                                ]);

                                $stockUpdatedCount++;
                            }

                            $updatedCount++;

                        } catch (\Throwable $e) {
                            $errorCount++;

                            $this->logger->error('Save failed', [
                                'sku'     => $sku,
                                'message' => $e->getMessage(),
                                'trace'   => $e->getTraceAsString()
                            ]);

                            continue;
                        }
                    

                } else {

                    $unchangedCount++;

                    $this->logger->info('Product unchanged', [
                        'sku' => $sku
                    ]);

                }

            } catch (\Throwable $e) {

                $errorCount++;

                $this->logger->error('❌ Price update failed', [
                    'sku'   => $product->getSku(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        $this->logger->info('✅ PRICE CRON SUMMARY', [

            'checked'   => $checkedCount,
            'updated'   => $updatedCount,
            'stock_updated' => $stockUpdatedCount,
            'unchanged' => $unchangedCount,
            'errors'    => $errorCount

        ]);
    }

}
