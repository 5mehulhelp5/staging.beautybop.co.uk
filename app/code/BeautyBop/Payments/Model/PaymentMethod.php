<?php

declare(strict_types=1);

namespace BeautyBop\Payments\Model;

use Magento\Payment\Model\Method\AbstractMethod;

class PaymentMethod extends AbstractMethod
{
    /**
     * Payment method code.
     */
    public const CODE = 'beautybop_paypal';

    /**
     * Internal payment code.
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Is gateway.
     *
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * Can authorize.
     *
     * @var bool
     */
    protected $_canAuthorize = false;

    /**
     * Can capture.
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Can capture partial amounts.
     *
     * @var bool
     */
    protected $_canCapturePartial = false;

    /**
     * Can refund.
     *
     * @var bool
     */
    protected $_canRefund = false;

    /**
     * Can refund partial.
     *
     * @var bool
     */
    protected $_canRefundPartialPerInvoice = false;

    /**
     * Can void.
     *
     * @var bool
     */
    protected $_canVoid = false;

    /**
     * Can use checkout.
     *
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * Can use internally.
     *
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * Can use for multishipping.
     *
     * @var bool
     */
    protected $_canUseForMultishipping = false;

    /**
     * Is initialize needed.
     *
     * @var bool
     */
    protected $_isInitializeNeeded = false;
}