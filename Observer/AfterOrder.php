<?php

namespace Gravity\Observer;

use Gravity\Model\Config\Source\HookType;
use Magento\Framework\Event\Observer;
use Exception;

/**
 * Class AfterOrder
 * @package Gravity\Observer
 */
class AfterOrder extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::NEW_ORDER;
    /**
     * @var string
     */
    protected $hookTypeUpdate = HookType::UPDATE_ORDER;
    
    /**
     * @param Observer $observer
     *
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getDataObject();
        if ($item->getQGNew()) {
            parent::execute($observer);
        } else {
            $this->updateObserver($observer);
        }
    }
}

