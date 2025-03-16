<?php
/**
 * Gravity configuration model
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Gravity\Api\ConfigInterface;

/**
 * Configuration model
 */
class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function isDebugEnabled(?int $storeId = null): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_DEBUG,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getOrganizationId(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ORG_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getClientId(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getClientSecret(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_SECRET,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getHooksUrlProducts(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOOKS_URL_PRODUCTS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getHooksUrlOrders(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_HOOKS_URL_ORDERS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @inheritdoc
     */
    public function getUrlToken(?int $storeId = null): ?string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_URL_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}

