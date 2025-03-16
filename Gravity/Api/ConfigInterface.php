<?php
/**
 * Gravity configuration interface
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Api;

/**
 * Interface for module configuration
 */
interface ConfigInterface
{
    /**
     * Configuration path constants
     */
    const XML_PATH_ENABLED = 'gravity/general/enabled';
    const XML_PATH_DEBUG = 'gravity/general/debug';
    const XML_PATH_ORG_ID = 'gravity/general/x_org_id';
    const XML_PATH_CLIENT_ID = 'gravity/general/client_id';
    const XML_PATH_CLIENT_SECRET = 'gravity/general/client_secret';
    const XML_PATH_HOOKS_URL_PRODUCTS = 'gravity/general/hooks_url_products';
    const XML_PATH_HOOKS_URL_ORDERS = 'gravity/general/hooks_url_orders';
    const XML_PATH_URL_TOKEN = 'gravity/general/url_token';

    /**
     * Check if module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool;

    /**
     * Check if debug mode is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDebugEnabled(?int $storeId = null): bool;

    /**
     * Get Organization ID
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getOrganizationId(?int $storeId = null): ?string;

    /**
     * Get Client ID
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getClientId(?int $storeId = null): ?string;

    /**
     * Get Client Secret
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getClientSecret(?int $storeId = null): ?string;

    /**
     * Get Hooks URL for Products
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getHooksUrlProducts(?int $storeId = null): ?string;

    /**
     * Get Hooks URL for Orders
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getHooksUrlOrders(?int $storeId = null): ?string;

    /**
     * Get URL for token authentication
     *
     * @param int|null $storeId
     * @return string|null
     */
    public function getUrlToken(?int $storeId = null): ?string;
}

