<?php

namespace Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Gravity\Model\Config\Source\HookType;
use Magento\Framework\DataObject;
use Gravity\Helper\Data;

class AfterImportDataObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * AfterImportDataObserver constructor.
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->helper->isEnabled()) {
            return;
        }
        
        $this->import = $observer->getEvent()->getAdapter();
        if ($products = $observer->getEvent()->getBunch()) {
            foreach ($products as $product) {
                $newSku = $this->import->getNewSku($product[ImportProduct::COL_SKU]);
                $item = new DataObject([
                    'sku'  => $newSku
                ]);
                $this->helper->send($item, HookType::UPDATE_PRODUCT);
            }
        }
    }
}

