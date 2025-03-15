<?php
declare(strict_types=1);

namespace Quickcomm\Gravity\Plugin;

use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
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
     * Plugin before execute
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
        // Obtener el Store ID actual
        $storeId = $this->storeManager->getStore()->getId();

        // Verificar si el módulo está habilitado para la tienda actual
        if (!$this->helper->isEnabled($storeId)) {
            return;
        }

        // Verificar si la configuración permite restar cantidad
        if (!$this->stockConfiguration->canSubtractQty($storeId)) {
            return;
        }

        foreach ($reservations as $reservation) {
            // Obtener configuración del artículo en stock
            $stockItemConfiguration = $this->getStockItemConfiguration->execute(
                $reservation->getSku(),
                $reservation->getStockId()
            );

            // Si el stock está gestionado, envía el hook correspondiente
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
