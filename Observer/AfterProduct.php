<?php
namespace Gravity\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Gravity\Model\Config\Source\HookType;

/**
 * Class AfterProduct
 * @package Gravity\Observer
 */
class AfterProduct extends AfterSave
{
    /**
     * @var string
     */
    protected $hookType = HookType::NEW_PRODUCT;

    /**
     * @var string
     */
    protected $hookTypeUpdate = HookType::UPDATE_PRODUCT;

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

