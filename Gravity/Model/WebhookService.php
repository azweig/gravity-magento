<?php
/**
 * Gravity webhook service
 *
 * @category  Gravity
 * @package   Gravity
 * @author    Gravity Team <support@gravity.com>
 * @copyright Copyright (c) 2023 Gravity (https://www.gravity.com)
 */
declare(strict_types=1);

namespace Gravity\Model;

use Exception;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Store\Model\StoreManagerInterface;
use Gravity\Api\ConfigInterface;
use Gravity\Api\WebhookServiceInterface;
use Gravity\Logger\Logger;
use Gravity\Model\Config\Source\ContentType;
use Gravity\Model\Config\Source\HookType;
use Gravity\Model\Config\Source\Method;

/**
 * Webhook service implementation
 */
class WebhookService implements WebhookServiceInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * WebhookService constructor.
     *
     * @param ConfigInterface $config
     * @param CurlFactory $curlFactory
     * @param CacheInterface $cache
     * @param Logger $logger
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ConfigInterface $config,
        CurlFactory $curlFactory,
        CacheInterface $cache,
        Logger $logger,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->curlFactory = $curlFactory;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }

    /**
     * Get store ID from item or current store
     *
     * @param DataObject $item
     * @return int
     * @throws NoSuchEntityException
     */
    private function getItemStoreId(DataObject $item): int
    {
        if (method_exists($item, 'getData') && $item->getData('store_id')) {
            return (int)$item->getData('store_id');
        }

        return (int)$this->storeManager->getStore()->getId();
    }

    /**
     * @inheritdoc
     */
    public function send(DataObject $item, string $hookType)
    {
        $storeId = $this->getItemStoreId($item);

        if (!$this->config->isEnabled($storeId)) {
            return false;
        }

        if (!$this->config->getClientId($storeId) || 
            !$this->config->getOrganizationId($storeId) || 
            !$this->config->getClientSecret($storeId)) {
            $this->logger->critical('Missing configuration, fill out all required information before continue');
            return false;
        }
        
        $result = [];
        try {
            $result = $this->sendWebhook($item, $hookType);
        } catch (Exception $e) {
            $this->logger->critical($e);
            $result = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getAuthToken(?int $storeId = null)
    {
        $url = $this->config->getUrlToken($storeId) ?: self::DEFAULT_AUTH_ENDPOINT;

        $header = [
            [
                'name' => 'Accept',
                'value' => ContentType::APPLICATION_JSON
            ]
        ];
        $contentType = ContentType::APPLICATION_X_WWW_FORM_URLENCODE;
        $method = Method::POST;

        $body = [
            'grant_type' => 'client_credentials',
            'scope' => 'api IdentityServerApi',
            'client_id' => $this->config->getClientId($storeId),
            'client_secret' => $this->config->getClientSecret($storeId),
            'x-Org-Id' => $this->config->getOrganizationId($storeId)
        ];
        $body = http_build_query($body);
        
        $result = $this->sendHttpRequest($header, false, $contentType, $url, $body, $method);

        $auth = json_decode($result['response'], true);

        return $auth["access_token"] ?? false;
    }

    /**
     * @inheritdoc
     */
    public function sendWebhook(DataObject $item, string $hookType)
    {
        $storeId = $this->getItemStoreId($item);
        $bear = $this->cache->load(self::AUTH_TOKEN_CACHE_ID);
        
        if (!$bear) {
            $bear = $this->getAuthToken($storeId);
            $this->cache->save($bear, self::AUTH_TOKEN_CACHE_ID, [], 86400);
        }

        if (!$bear) {
            return false;
        }

        $orderId = false;
        $url = $this->config->getHooksUrlProducts($storeId);
        $eventType = 'ProductCreated';
        
        switch ($hookType) {
            case HookType::NEW_PRODUCT:
                $eventType = 'ProductCreated';
                $url = $this->config->getHooksUrlProducts($storeId);
                break;
            case HookType::DELETE_PRODUCT:
                $eventType = 'ProductDeleted';
                $url = $this->config->getHooksUrlProducts($storeId);
                break;
            case HookType::UPDATE_PRODUCT:
                $eventType = 'ProductUpdated';
                $url = $this->config->getHooksUrlProducts($storeId);
                break;
            case HookType::NEW_ORDER:
                $eventType = 'OrderCreated';
                $url = $this->config->getHooksUrlOrders($storeId);
                $orderId = $item->getIncrementId();
                break;
            case HookType::UPDATE_ORDER:
                $eventType = 'OrderUpdated';
                $url = $this->config->getHooksUrlOrders($storeId);
                $orderId = $item->getIncrementId();
                break;
            case HookType::UPDATE_QTY:
                $eventType = 'ProductSkuStockUpdated';
                $url = $this->config->getHooksUrlProducts($storeId);
                break;
            default:
                $eventType = 'Unknown';
                $url = $this->config->getHooksUrlProducts($storeId);
        }
        
        if (!$url) {
            $this->logger->critical('Missing webhook URL configuration');
            return false;
        }
        
        $body = [
            'data' => $item->getData(),
            'eventType' => $eventType
        ];
        
        if ($orderId) {
            $body['data'] = array_merge(['OrderId' => $orderId], $body['data']);
        }
        
        $body = json_encode($body);

        $contentType = ContentType::APPLICATION_JSON;
        $header = [
            [
                'name' => 'Bearer',
                'value' => $bear
            ]
        ];
        $method = Method::POST;

        return $this->sendHttpRequest($header, false, $contentType, $url, $body, $method);
    }

    /**
     * Send HTTP request
     *
     * @param array $headers
     * @param bool $authentication
     * @param string $contentType
     * @param string $url
     * @param string $body
     * @param string $method
     * @return array
     * @throws LocalizedException
     */
    private function sendHttpRequest(array $headers, bool $authentication, string $contentType, string $url, string $body, string $method): array
    {
        $curl = $this->curlFactory->create();
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $body
        ];

        // Set content type header
        $curlHeaders = [];
        $curlHeaders[] = 'Content-Type: ' . $contentType;
        $curlHeaders[] = 'Content-Length: ' . strlen($body);

        // Add custom headers
        foreach ($headers as $header) {
            if (isset($header['name']) && isset($header['value'])) {
                if ($header['name'] === 'Bearer') {
                    $curlHeaders[] = 'Authorization: Bearer ' . $header['value'];
                } else {
                    $curlHeaders[] = $header['name'] . ': ' . $header['value'];
                }
            }
        }

        $options[CURLOPT_HTTPHEADER] = $curlHeaders;

        // Set the options
        $curl->setOptions($options);

        // Execute the request
        $curl->write($method, $url, '1.1');
        $response = $curl->read();
        $curl->close();

        // Process the response
        $responseCode = 0;
        $responseBody = '';

        if ($response) {
            $responseArray = explode("\r\n\r\n", $response, 2);
            $responseBody = $responseArray[1] ?? '';
            preg_match('/HTTP\/[\d.]+\s+(\d+)/', $response, $matches);
            $responseCode = (int)($matches[1] ?? 0);
        }

        if ($this->config->isDebugEnabled()) {
            $this->logger->debug('Request  ?? 0);
        }

        if ($this->config->isDebugEnabled()) {
            $this->logger->debug('Request: ' . $url . ' - Method: ' . $method);
            $this->logger->debug('Request Body: ' . $body);
            $this->logger->debug('Response Code: ' . $responseCode);
            $this->logger->debug('Response: ' . $responseBody);
        }

        return [
            'success' => $responseCode >= 200 && $responseCode < 300,
            'code' => $responseCode,
            'response' => $responseBody
        ];
    }
}

