<?php
/**
 * Gravity before save observer
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
use Gravity\Helper\Data;

/**
 * Before save observer
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
     * @inheritdoc
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

