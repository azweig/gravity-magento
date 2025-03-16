<?php
/**
 * Gravity custom before save observer
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
use Magento\Framework\Exception\NoSuchEntityException;
use Gravity\Helper\Data;

/**
 * Custom before save observer
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
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $storeId = $this->helper->getStoreId();

        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        $item = $observer->getEvent()->getOrder();
        if ($item && $item->isObjectNew()) {
            $item->setQGNew(1);
        }
    }
}

