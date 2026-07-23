<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Psr\Log\LoggerInterface;

class WebsiteSearch
{
    private const SEARCH_URL =
        'https://www.beautyfort.com/products/browse'
        . '?sort=az'
        . '&category='
        . '&brands='
        . '&productTypes='
        . '&genders='
        . '&minPrice='
        . '&maxPrice='
        . '&minQty=0'
        . '&starRatings='
        . '&q=%s'
        . '&s=SEARCH';

    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Search BeautyFort website for a SKU and return the preview ID.
     *
     * Example:
     *
     * K230265
     *
     * returns
     *
     * 88408
     */
    public function findPreviewId(string $sku): ?string
    {
        $cookieFile = BP . '/var/beautyfort.cookies';

        $url = sprintf(
            self::SEARCH_URL,
            urlencode($sku)
        );

        $this->logger->info('Searching BeautyFort website', [
            'sku' => $sku,
            'url' => $url
        ]);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_TIMEOUT        => 30,
        ]);

        $html = curl_exec($ch);

        if ($html === false) {

            $this->logger->error('Website search failed', [
                'sku'   => $sku,
                'error' => curl_error($ch)
            ]);

            curl_close($ch);

            return null;
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        file_put_contents(
            BP . '/var/log/beautyfort-search.html',
            $html
        );

        if ($httpCode !== 200) {

            $this->logger->error('Unexpected search response', [
                'sku'       => $sku,
                'http_code' => $httpCode
            ]);

            return null;
        }

        if (!preg_match('#/pic/preview/([0-9]+)#', $html, $matches)) {

            $this->logger->warning('Preview ID not found', [
                'sku' => $sku
            ]);

            return null;
        }

        $previewId = $matches[1];

        $this->logger->info('Preview ID found', [
            'sku'        => $sku,
            'preview_id' => $previewId
        ]);

        return $previewId;
    }
}