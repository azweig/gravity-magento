<?php
/**
 * Gravity order status observer
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

/**
 * Set order status for PGravity payment method
 */
class SetOrderStatusForPGravity implements ObserverInterface
{
    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        
        // Check if payment method is PGravity
        if ($order->getPayment()->getMethod() === 'pgravity') {
            // Set order status to confirmed (processing)
            $order->setState(Order::STATE_PROCESSING);
            $order->setStatus('processing');
        }
    }
}

