<?php

declare(strict_types=1);

namespace BeautyFort\BeautyfortProductImport\Model;

use Psr\Log\LoggerInterface;

class HighResImageService
{
    /**
     * @var WebsiteLogin
     */
    private $websiteLogin;

    /**
     * @var WebsiteSearch
     */
    private $websiteSearch;

    /**
     * @var PreviewParser
     */
    private $previewParser;


    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        WebsiteLogin $websiteLogin,
        WebsiteSearch $websiteSearch,
        PreviewParser $previewParser,
        LoggerInterface $logger
    ) {
        $this->websiteLogin = $websiteLogin;
        $this->websiteSearch = $websiteSearch;
        $this->previewParser = $previewParser;
        $this->logger = $logger;
    }

    /**
     * Returns the downloaded high-resolution image.
     */
    public function getImageUrlForSku(string $sku): ?string
    {

        if (!$this->websiteLogin->login()) {

            return null;
        }

        $previewId = $this->websiteSearch->findPreviewId($sku);

        if (!$previewId) {

            return null;
        }

        $imageUrl = $this->previewParser->getImageUrl($previewId);

        if (!$imageUrl) {

            return null;
        }

        return $imageUrl;
    }
}