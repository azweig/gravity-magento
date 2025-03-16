<?php
/**
 * Gravity after save observer
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Gravity\Helper\Data;

/**
 * Abstract after save observer
 */
abstract class AfterSave implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var string
     */
    protected $hookType = '';

    /**
     * @var string
     */
    protected $hookTypeUpdate = '';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * AfterSave constructor.
     *
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param Data $helper
     */
    public function __construct(
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        Data $helper
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getDataObject();
        $this->helper->send($item, $this->hookType);
    }

    /**
     * Update observer
     *
     * @param Observer $observer
     * @throws Exception
     */
    protected function updateObserver(Observer $observer)
    {
        $item = $observer->getDataObject();
        $this->helper->send($item, $this->hookTypeUpdate);
    }
}

