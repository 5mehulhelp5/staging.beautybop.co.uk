<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Psr\Log\LoggerInterface;

class ImageDownloader
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
     * Download a high-resolution image.
     */
    public function download(string $imageUrl, string $sku): ?string
    {
        $cookieFile = BP . '/var/beautyfort.cookies';

        $directory = BP . '/var/import/highres';

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $filename = $directory . '/' . $sku . '.jpg';

        $fp = fopen($filename, 'w');

        if (!$fp) {
            $this->logger->error('Unable to create image file', [
                'file' => $filename
            ]);

            return null;
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $imageUrl,
            CURLOPT_FILE           => $fp,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_COOKIEFILE     => $cookieFile,
            CURLOPT_COOKIEJAR      => $cookieFile,
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_TIMEOUT        => 60,
        ]);

        curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {

            $this->logger->error('Image download failed', [
                'sku'   => $sku,
                'error' => curl_error($ch)
            ]);

            fclose($fp);
            curl_close($ch);

            return null;
        }

        fclose($fp);
        curl_close($ch);

        if ($httpCode !== 200) {

            $this->logger->warning('Unexpected HTTP response downloading image', [
                'sku'       => $sku,
                'http_code' => $httpCode
            ]);

            return null;
        }

        $this->logger->info('High-resolution image downloaded', [
            'sku'  => $sku,
            'file' => $filename
        ]);

        return $filename;
    }
}