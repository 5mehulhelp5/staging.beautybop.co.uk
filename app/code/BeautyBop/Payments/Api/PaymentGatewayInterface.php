<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Api;

interface PaymentGatewayInterface
{
    /**
     * Create payment order.
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array;


    /**
     * Capture payment.
     *
     * @param string $paymentId
     * @return array
     */
    public function capture(string $paymentId): array;


    /**
     * Refund payment.
     *
     * @param string $transactionId
     * @param float $amount
     * @return array
     */
    public function refund(
        string $transactionId,
        float $amount
    ): array;


    /**
     * Cancel payment.
     *
     * @param string $paymentId
     * @return bool
     */
    public function cancel(string $paymentId): bool;
}