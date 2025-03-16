<?php
namespace Gravity\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\DataObject;

/**
 * Class PGravity - Payment method for Gravity API
 */
class PGravity extends AbstractMethod
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'pgravity';

    /**
     * Payment Method features
     */
    protected $_isGateway = false;
    protected $_canOrder = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = false;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = false;
    protected $_canFetchTransactionInfo = false;
    protected $_canReviewPayment = false;
    protected $_canRefundInvoicePartial = false;

    /**
     * Check whether payment method can be used
     *
     * @param CartInterface|null $quote
     * @return bool
     */
    public function isAvailable(CartInterface $quote = null)
    {
        // Only available for admin/API, not for frontend
        return $this->_canUseInternal;
    }

    /**
     * Authorize payment method
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $payment->setStatus('APPROVED')
            ->setTransactionId($this->generateTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }

    /**
     * Capture payment method
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount)
    {
        $payment->setStatus('APPROVED')
            ->setTransactionId($this->generateTransactionId())
            ->setIsTransactionClosed(1);

        return $this;
    }

    /**
     * Generate a unique transaction ID
     *
     * @return string
     */
    private function generateTransactionId()
    {
        return 'gravity_' . uniqid();
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return 'Gravity Payment Method for API use only';
    }
}

