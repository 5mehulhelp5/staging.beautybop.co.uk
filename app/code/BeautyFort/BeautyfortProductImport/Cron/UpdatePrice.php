<?php

namespace BeautyFort\BeautyfortProductImport\Cron;

use BeautyFort\BeautyfortProductImport\Model\PriceUpdater;

class UpdatePrice
{
    /** @var PriceUpdater */
    private $priceUpdater;

    public function __construct(
        PriceUpdater $priceUpdater
    ) {
        $this->priceUpdater = $priceUpdater;
    }

   public function execute(): void
    {
         file_put_contents(
            BP . '/var/log/updateprice.log',
            "START " . date('Y-m-d H:i:s') . PHP_EOL,
            FILE_APPEND
        );

        $this->priceUpdater->execute();

        file_put_contents(
            BP . '/var/log/updateprice.log',
            "END " . date('Y-m-d H:i:s') . PHP_EOL,
            FILE_APPEND
        );
    }
}
