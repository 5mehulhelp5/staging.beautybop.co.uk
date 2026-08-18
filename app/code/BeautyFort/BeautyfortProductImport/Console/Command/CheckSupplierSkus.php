<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Console\Command;

use BeautyFort\BeautyfortProductImport\Helper\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckSupplierSkus extends Command
{
    private const ARG_FILE = 'file';

    private Api $api;

    public function __construct(
        Api $api
    ) {
        $this->api = $api;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('beautyfort:products:check-skus')
            ->setDescription(
                'Check a CSV list of SKUs against the current BeautyFort stock catalogue'
            )
            ->addArgument(
                self::ARG_FILE,
                InputArgument::REQUIRED,
                'CSV file containing a sku column'
            );

        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $file = (string)$input->getArgument(self::ARG_FILE);

        /*
         * Allow paths relative to Magento root.
         */
        if (!str_starts_with($file, '/')) {
            $file = BP . '/' . ltrim($file, '/');
        }

        if (!is_file($file)) {
            $output->writeln(
                '<error>CSV file not found: ' . $file . '</error>'
            );

            return Command::FAILURE;
        }

        /*
         * Download current BeautyFort catalogue.
         */
        $output->writeln('<info>Downloading BeautyFort stock file...</info>');

        $supplierProducts = $this->api->getStockFile();

        if (empty($supplierProducts)) {
            $output->writeln(
                '<error>No supplier products returned. Aborting check.</error>'
            );

            return Command::FAILURE;
        }

        /*
         * Build lookup in exactly the same way as PriceUpdater.
         */
        $supplierLookup = [];

        foreach ($supplierProducts as $item) {

            if (empty($item['StockCode'])) {
                continue;
            }

            $stockCode = strtoupper(
                trim((string)$item['StockCode'])
            );

            $supplierLookup[$stockCode] = $item;
        }

        $output->writeln(
            '<info>BeautyFort catalogue contains ' .
            count($supplierLookup) .
            ' SKUs.</info>'
        );

        /*
         * Read requested SKUs.
         */
        $handle = fopen($file, 'r');

        if ($handle === false) {
            $output->writeln(
                '<error>Unable to open CSV file.</error>'
            );

            return Command::FAILURE;
        }

        $header = fgetcsv($handle);

        if (!$header) {
            fclose($handle);

            $output->writeln(
                '<error>CSV file is empty.</error>'
            );

            return Command::FAILURE;
        }

        $skuColumn = array_search('sku', $header, true);

        if ($skuColumn === false) {
            fclose($handle);

            $output->writeln(
                '<error>CSV must contain a "sku" column.</error>'
            );

            return Command::FAILURE;
        }

        $found = [];
        $notFound = [];
        $checked = 0;

        while (($row = fgetcsv($handle)) !== false) {

            if (!isset($row[$skuColumn])) {
                continue;
            }

            $sku = trim((string)$row[$skuColumn]);

            if ($sku === '') {
                continue;
            }

            $checked++;

            $lookupSku = strtoupper($sku);

            if (isset($supplierLookup[$lookupSku])) {

                $supplierData = $supplierLookup[$lookupSku];

                $found[] = [
                    'sku'   => $sku,
                    'price' => $supplierData['Price'] ?? '',
                    'rrp'   => $supplierData['RRP'] ?? '',
                    'stock' => $supplierData['StockLevel'] ?? '',
                ];

            } else {

                $notFound[] = $sku;
            }
        }

        fclose($handle);

        /*
         * Results
         */
        $output->writeln('');
        $output->writeln('<comment>==============================</comment>');
        $output->writeln('<comment>BeautyFort SKU Check</comment>');
        $output->writeln('<comment>==============================</comment>');

        $output->writeln('Checked:   ' . $checked);
        $output->writeln('Found:     ' . count($found));
        $output->writeln('Not found: ' . count($notFound));

        $output->writeln('');

        if ($found) {

            $output->writeln('<info>FOUND IN BEAUTYFORT</info>');

            foreach ($found as $item) {

                $output->writeln(
                    sprintf(
                        '%s | Price: %s | RRP: %s | Stock: %s',
                        $item['sku'],
                        $item['price'],
                        $item['rrp'],
                        $item['stock']
                    )
                );
            }
        }

        if ($notFound) {

            $output->writeln('');
            $output->writeln('<error>NOT FOUND IN BEAUTYFORT</error>');

            foreach ($notFound as $sku) {
                $output->writeln($sku);
            }
        }

        return Command::SUCCESS;
    }
}