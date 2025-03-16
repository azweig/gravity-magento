<?php
/**
 * Gravity stock plugin
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Plugin;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\Store\Model\StoreManagerInterface;
use Gravity\Helper\Data;
use Gravity\Model\Config\Source\HookType;

/**
 * Stock plugin
 */
class StockPlugin
{
    /**
     * @var string
     */
    protected $hookTypeUpdate = HookType::UPDATE_QTY;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * StockPlugin constructor.
     *
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param StockConfigurationInterface $stockConfiguration
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        StockConfigurationInterface $stockConfiguration,
        Data $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->stockConfiguration = $stockConfiguration;
        $this->storeManager = $storeManager;
    }

    /**
     * Before execute plugin
     *
     * @param AppendReservationsInterface $subject
     * @param array $reservations
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws SkuIsNotAssignedToStockException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(AppendReservationsInterface $subject, array $reservations)
    {
        $storeId = $this->storeManager->getStore()->getId();

        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        if (!$this->stockConfiguration->canSubtractQty($storeId)) {
            return;
        }

        foreach ($reservations as $reservation) {
            $stockItemConfiguration = $this->getStockItemConfiguration->execute(
                $reservation->getSku(),
                $reservation->getStockId()
            );

            if ($stockItemConfiguration->isManageStock()) {
                $item = new DataObject([
                    'sku' => $reservation->getSku(),
                    'store_id' => $storeId
                ]);
                $this->helper->send($item, $this->hookTypeUpdate);
            }
        }
    }
}

