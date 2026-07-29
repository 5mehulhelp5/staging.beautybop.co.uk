<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Psr\Log\LoggerInterface;

class PreviewParser
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * Download the BeautyFort preview page.
     *
     * Returns null for now.
     */
    public function getImageUrl(string $previewId): ?string
    {
        $cookieFile = BP . '/var/beautyfort.cookies';

        $url = sprintf(
            'https://www.beautyfort.com/pic/preview/%s',
            $previewId
        );

        $this->logger->info('Downloading preview page', [
            'preview_id' => $previewId,
            'url'        => $url
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

            $this->logger->error('Failed downloading preview page', [
                'preview_id' => $previewId,
                'error'      => curl_error($ch)
            ]);

            curl_close($ch);

            return null;

           
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        file_put_contents(
            BP . '/var/log/beautyfort-preview.html',
            $html
        );

        $this->logger->info('Preview page downloaded', [
            'preview_id' => $previewId,
            'http_code'  => $httpCode
        ]);


         if (!preg_match(
            '/<img\b[^>]*\bsrc\s*=\s*["\']([^"\']+)["\']/i',
            $html,
            $matches
        )) {

            $this->logger->warning(
                'Image URL not found',
                [
                    'preview_id' => $previewId
                ]
            );

            return null;
        }

        $imageUrl = html_entity_decode($matches[1]);

        $this->logger->info(
            'Image URL found',
            [
                'preview_id' => $previewId,
                'image_url'  => $imageUrl
            ]
        );

        return $imageUrl;

    }
}