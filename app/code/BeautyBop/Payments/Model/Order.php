<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

class Order
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $approveUrl;

    public function __construct(
        string $id,
        string $status,
        string $approveUrl
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->approveUrl = $approveUrl;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getApproveUrl(): string
    {
        return $this->approveUrl;
    }
}