<?php
/**
 * Gravity data helper
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Gravity\Api\ConfigInterface;
use Gravity\Api\WebhookServiceInterface;
use Gravity\Logger\Logger;

/**
 * Data helper
 */
class Data extends AbstractHelper
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var WebhookServiceInterface
     */
    private $webhookService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ConfigInterface $config
     * @param WebhookServiceInterface $webhookService
     * @param Logger $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ConfigInterface $config,
        WebhookServiceInterface $webhookService,
        Logger $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->webhookService = $webhookService;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get store ID from item or current store
     *
     * @param DataObject|null $item
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(?DataObject $item = null): int
    {
        if ($item && method_exists($item, 'getData') && $item->getData('store_id')) {
            return (int)$item->getData('store_id');
        }

        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->config->isEnabled($storeId);
    }

    /**
     * Send webhook for an entity
     *
     * @param DataObject $item
     * @param string $hookType
     * @return array|bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function send(DataObject $item, string $hookType)
    {
        $storeId = $this->getStoreId($item);

        if (!$this->isEnabled($storeId)) {
            return false;
        }

        try {
            return $this->webhookService->send($item, $hookType);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage(), ['exception' => $e]);
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

