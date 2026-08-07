<?php

namespace BeautyBop\Core\Block\Homepage;

use Magento\Framework\View\Element\Template;
use FishPig\WordPress\Model\ResourceModel\Post\CollectionFactory;

class LatestPosts extends Template
{
    protected $postCollectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $postCollectionFactory,
        array $data = []
    ) {
        $this->postCollectionFactory = $postCollectionFactory;

        parent::__construct($context, $data);
    }

    public function getLatestPosts($limit = 3)
    {
        $collection = $this->postCollectionFactory->create();

        $collection
            ->addFieldToFilter('post_status', 'publish')
            ->addFieldToFilter('post_type', 'post')
            ->setOrder('post_date', 'DESC')
            ->setPageSize($limit);

        return $collection;
    }
}