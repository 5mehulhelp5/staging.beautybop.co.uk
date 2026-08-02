<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Gateway;

class PayPalGateway extends AbstractGateway
{
    /**
     * Create a PayPal order.
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array
    {
        $this->log(
            'Creating PayPal order.',
            [
                'gateway' => 'paypal',
                'payload' => $data
            ]
        );

        return [
            'success' => true,
            'message' => 'PayPal order created (placeholder).'
        ];
    }

    /**
     * Capture a PayPal payment.
     *
     * @param string $paymentId
     * @return array
     */
    public function capture(string $paymentId): array
    {
        $this->log(
            'Capturing PayPal payment.',
            [
                'gateway' => 'paypal',
                'payment_id' => $paymentId
            ]
        );

        return [
            'success' => true,
            'message' => 'Payment captured (placeholder).'
        ];
    }

    /**
     * Refund a payment.
     *
     * @param string $transactionId
     * @param float $amount
     * @return array
     */
    public function refund(
        string $transactionId,
        float $amount
    ): array {

        $this->log(
            'Refunding PayPal payment.',
            [
                'gateway' => 'paypal',
                'transaction_id' => $transactionId,
                'amount' => $amount
            ]
        );

        return [
            'success' => true,
            'message' => 'Refund completed (placeholder).'
        ];
    }

    /**
     * Cancel a PayPal order.
     *
     * @param string $paymentId
     * @return bool
     */
    public function cancel(string $paymentId): bool
    {
        $this->log(
            'Cancelling PayPal order.',
            [
                'gateway' => 'paypal',
                'payment_id' => $paymentId
            ]
        );

        return true;
    }
}