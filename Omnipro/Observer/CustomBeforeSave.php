<?php
namespace Omnipro\Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Quickcomm\Gravity\Helper\Data;

/**
 * Class CustomBeforeSave
 * @package Quickcomm\Gravity\Observer
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
     * @param Observer $observer
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $storeId = $this->helper->getStoreId();

        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        $item = $observer->getEvent()->getOrder();
        if ($item->isObjectNew()) {
            $item->setQGNew(1);
        }
    }
}
