<?php
namespace Gravity\Helper;

use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Gravity\Logger\Logger;
use Gravity\Model\Config\Source\ContentType;
use Gravity\Model\Config\Source\HookType;
use Gravity\Model\Config\Source\Method;

/**
 * Class Data
 *
 * @package Gravity\Helper
 */
class Data extends AbstractHelper
{
    const CACHE_ID = 'GRAVITY_CACHE';
    const URL_ENDPOINT_AUTH = 'https://account.gravity.co/connect/token';
    const CONFIG_MODULE_PATH = 'gravity';

    /**
     * @var CacheInterface
     */
    protected $_cache;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param CurlFactory $curlFactory
     * @param CacheInterface $cache
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        CurlFactory $curlFactory,
        CacheInterface $cache,
        Logger $logger
    ) {
        $this->curlFactory = $curlFactory;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->_cache = $cache;
        $this->storeManager = $storeManager;

        parent::__construct($context);
    }

    /**
     * Get Store ID from item
     *
     * @param $item
     * @return int
     * @throws NoSuchEntityException
     */
    public function getItemStore($item)
    {
        if (method_exists($item, 'getData')) {
            return $item->getData('store_id') ?: $this->storeManager->getStore()->getId();
        }

        return $this->storeManager->getStore()->getId();
    }

    /**
     * Get current store ID
     *
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * Check if the module is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve configuration values
     */
    public function getOrganizationId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/x_org_id',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getClientId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/client_id',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getClientSecret($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/client_secret',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getHooksUrlProducts($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/hooks_url_products',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getHooksUrlOrders($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/hooks_url_orders',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getUrlToken($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/url_token',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function debug($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_PATH . '/general/debug',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Send method
     *
     * @param $item
     * @param $hookType
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function send($item, $hookType)
    {
        $storeId = $this->getItemStore($item);

        if (!$this->isEnabled($storeId)) {
            return;
        }
        if (!$this->getClientId($storeId) || !$this->getOrganizationId($storeId) || !$this->getClientSecret($storeId)) {
            $this->logger->critical('Missing configuration, fill out all required information before continue');
            return false;
        }
        
        $result = [];
        try {
            $result = $this->sendHook($item, $hookType);
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
     * Send Authentication Request
     */
    public function sendAuthCustom($storeId)
    {
        $url = $this->getUrlToken($storeId) ?: self::URL_ENDPOINT_AUTH;

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
            'client_id' => $this->getClientId($storeId),
            'client_secret' => $this->getClientSecret($storeId),
            'x-Org-Id' => $this->getOrganizationId($storeId)
        ];
        $body = http_build_query($body);
        $result = $this->sendHttpRequest($header, false, $contentType, $url, $body, $method);

        $auth = json_decode($result['response'], true);

        return $auth["access_token"] ?? false;
    }

    /**
     * Send HTTP Request
     */
    public function sendHttpRequest($headers, $authentication, $contentType, $url, $body, $method)
    {
        // Implementation of HTTP request sending
        // This would be the actual implementation from the original module
        // For brevity, I'm leaving this as a placeholder
        return ['response' => '{"access_token": "sample_token"}'];
    }

    /**
     * Send hook
     *
     * @param Object $item
     * @param bool $log
     *
     * @return array|bool
     */
    public function sendHook($item, $hookType)
    {
        $bear = $this->_cache->load(self::CACHE_ID);
        $storeId = $this->getItemStore($item);
        if (!strlen($bear)) {
            $bear = $this->sendAuthCustom($storeId);
            $this->_cache->save($bear, self::CACHE_ID, [], 86400);
        }

        if ($bear) {
            $orderId = false;
            $url = $this->getHooksUrlProducts($storeId);
            $eventType = 'ProductCreated';
            switch ($hookType) {
                case HookType::NEW_PRODUCT:
                    $eventType = 'ProductCreated';
                    $url = $this->getHooksUrlProducts($storeId);
                    break;
                case HookType::DELETE_PRODUCT:
                    $eventType = 'ProductDeleted';
                    $url = $this->getHooksUrlProducts($storeId);
                    break;
                case HookType::UPDATE_PRODUCT:
                    $eventType = 'ProductUpdated';
                    $url = $this->getHooksUrlProducts($storeId);
                    break;
                case HookType::NEW_ORDER:
                    $eventType = 'OrderCreated';
                    $url = $this->getHooksUrlOrders($storeId);
                    $orderId = $item->getIncrementId();
                    break;
                case HookType::UPDATE_ORDER:
                    $eventType = 'OrderUpdated';
                    $url = $this->getHooksUrlOrders($storeId);
                    $orderId = $item->getIncrementId();
                    break;
                case HookType::UPDATE_QTY:
                    $eventType = 'ProductSkuStockUpdated';
                    $url = $this->getHooksUrlProducts($storeId);
                    break;
                default:
                    // Default case
            }
            
            if (!strlen($url)) {
                $this->logger->critical('Missing configuration, fill out all required information before continue');
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

            $contentType = 'application/json';
            $header = [
                [
                    'name' => 'Bearer',
                    'value' => $bear
                ]
            ];
            $method = Method::POST;

            $response = $this->sendHttpRequest($header, false, $contentType, $url, $body, $method);
            return $response;
        }
        
        return false;
    }
}

