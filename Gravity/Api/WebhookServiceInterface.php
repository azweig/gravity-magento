<?php
/**
 * Gravity webhook service interface
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface for webhook service
 */
interface WebhookServiceInterface
{
    /**
     * Default authentication endpoint
     */
    const DEFAULT_AUTH_ENDPOINT = 'https://account.gravity.co/connect/token';

    /**
     * Cache identifier for auth token
     */
    const AUTH_TOKEN_CACHE_ID = 'GRAVITY_AUTH_TOKEN';

    /**
     * Send webhook for an entity
     *
     * @param DataObject $item
     * @param string $hookType
     * @return array|bool
     * @throws LocalizedException
     */
    public function send(DataObject $item, string $hookType);

    /**
     * Get authentication token
     *
     * @param int|null $storeId
     * @return string|bool
     */
    public function getAuthToken(?int $storeId = null);

    /**
     * Send webhook data
     *
     * @param DataObject $item
     * @param string $hookType
     * @return array|bool
     */
    public function sendWebhook(DataObject $item, string $hookType);
}

