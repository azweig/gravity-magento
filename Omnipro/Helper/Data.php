<?php

namespace Omnipro\Gravity\Helper;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\ScopeInterface;
use Quickcomm\Gravity\Helper\Data as QuickcommData;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\CacheInterface;
use Quickcomm\Gravity\Logger\Logger;
use Quickcomm\Gravity\Model\Config\Source\ContentType;
use Quickcomm\Gravity\Model\Config\Source\HookType;
use Quickcomm\Gravity\Model\Config\Source\Method;

/**
 * Class data helper
 */
class Data extends QuickcommData
{
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
        $this->storeManager = $storeManager;
        parent::__construct($context, $objectManager, $storeManager, $transportBuilder, $curlFactory, $cache, $logger);
    }

    /**
     * Override the getItemStore function
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
     * Get isEnabled configuration value
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getUrlToken configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getUrlToken($storeId = null) :?string
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/url_token',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getOrganizationId configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOrganizationId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/x_org_id',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getClientId configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getClientId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/client_id',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getClientSecret configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getClientSecret($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/client_secret',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getHooksUrlProducts configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getHooksUrlProducts($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/hooks_url_products',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get getHooksUrlOrders configuration value
     *
     * @param int|null $storeId
     * @return string
     */
    public function getHooksUrlOrders($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/hooks_url_orders',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get debug configuration value
     *
     * @param int|null $storeId
     * @return bool
     */
    public function debug($storeId = null)
    {
        return $this->scopeConfig->getValue(
            'quickcomm_gravity/general/debug',
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
     * Send request to webservice
     *
     * @param $item
     * @return string|bool
     * @throws NoSuchEntityException
     */
    public function sendAuthCustom($storeId)
    {
        $url = $this->getUrlToken($storeId);
        if ($url === null) {
            return false;
        }

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
        $storeId =  $this->getItemStore($item);
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
                case HookType::UPDATE_QTY:
                    $eventType = 'ProductSkuPriceUpdated';
                    $url = $this->getHooksUrlProducts($storeId);
                default:

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
