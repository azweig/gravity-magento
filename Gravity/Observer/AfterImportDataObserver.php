<?php
/**
 * Gravity after import data observer
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Observer;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Gravity\Helper\Data;
use Gravity\Model\Config\Source\HookType;

/**
 * After import data observer
 */
class AfterImportDataObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ImportProduct
     */
    private $import;

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
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        // Currently disabled
        return;
        
        $this->import = $observer->getEvent()->getAdapter();
        if ($products = $observer->getEvent()->getBunch()) {
            foreach ($products as $product) {
                $newSku = $this->import->getNewSku($product[ImportProduct::COL_SKU]);
                $item = new DataObject([
                    'sku' => $newSku
                ]);
                $this->helper->send($item, HookType::UPDATE_PRODUCT);
            }
        }
    }
}

