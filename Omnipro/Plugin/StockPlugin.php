<?php
declare(strict_types=1);

namespace Omnipro\Gravity\Plugin;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magento\Framework\Exception\LocalizedException;
use Quickcomm\Gravity\Helper\Data;
use Magento\Framework\DataObject;
use Quickcomm\Gravity\Model\Config\Source\HookType;
use Magento\Store\Model\StoreManagerInterface;

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
     * Plugn before execute
     *
     * @param AppendReservationsInterface $subject
     * @param ReservationInterface[] $reservations
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
                    'sku'  => $reservation->getSku(),
                    'store_id' => $storeId
                ]);
                $this->helper->send($item, $this->hookTypeUpdate);
            }
        }
    }
}
