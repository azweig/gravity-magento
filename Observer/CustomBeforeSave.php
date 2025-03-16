<?php

namespace Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Gravity\Helper\Data;

/**
 * Class CustomBeforeSave
 * @package Gravity\Observer
 */
class CustomBeforeSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomBeforeSave constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Execute method for the observer
     *
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $storeId = $this->helper->getStoreId();

        // Check if the module is enabled for the store
        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        // Access the order from the observer event
        $item = $observer->getEvent()->getOrder();
        if ($item && $item->isObjectNew()) {
            // Set a custom flag on the order
            $item->setQGNew(1);
        }
    }
}

