<?php

namespace Quickcomm\Gravity\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Quickcomm\Gravity\Model\Config\Source\HookType;
use Magento\Framework\DataObject;

class AfterImportDataObserver implements ObserverInterface
{
    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        return;
        $this->import = $observer->getEvent()->getAdapter();
        if ($products = $observer->getEvent()->getBunch()) {
            foreach ($products as $product) {
                $newSku = $this->import->getNewSku($$product[ImportProduct::COL_SKU]);
                $item = new DataObject([
                    'sku'  => $newSku
                ]);
                $this->helper->send($item, HookType::UPDATE_PRODUCT);
                
            }
        }
    }


}
