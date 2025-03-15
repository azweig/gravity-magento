<?php

namespace Quickcomm\Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Quickcomm\Gravity\Helper\Data;

/**
 * Class BeforeSave
 * @package Quickcomm\Gravity\Observer
 */
class BeforeSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * BeforeSave constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return;
        }
        $item = $observer->getDataObject();
        if ($item->isObjectNew()) {
            $item->setQGNew(1);
        }
    }
}
