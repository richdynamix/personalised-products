<?php

namespace Richdynamix\Suggest\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface as ScopeInterface;

/**
 * Class Config
 * @package Richdynamix\Suggest\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ScopeInterface
     */
    protected $_storeScope;

    /**
     *
     */
    const XML_PATH_ENABLED = 'suggest/general/enabled';
    /**
     *
     */
    const XML_PATH_ACCESS_KEY = 'suggest/general/access_key';
    /**
     *
     */
    const XML_PATH_ENGINE_URL = 'suggest/general/engine_url';
    /**
     *
     */
    const XML_PATH_ENGINE_PORT = 'suggest/general/engine_port';
    /**
     *
     */
    const XML_PATH_EVENT_SERVER_URL = 'suggest/general/event_url';
    /**
     *
     */
    const XML_PATH_EVENT_SERVER_PORT = 'suggest/general/event_port';
    /**
     *
     */
    const XML_PATH_PRODUCT_COUNT = 'suggest/general/product_count';


    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ScopeInterface $scope
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ScopeInterface $scope)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeScope = $scope::SCOPE_STORE;
    }

    /**
     * @return mixed
     */
    public function isEnabled() {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getAccessKey() {
        return $this->scopeConfig->getValue(self::XML_PATH_ACCESS_KEY, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getEngineUrl() {
        return $this->scopeConfig->getValue(self::XML_PATH_ENGINE_URL, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getEnginePort() {
        return $this->scopeConfig->getValue(self::XML_PATH_ENGINE_PORT, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getEventServerUrl() {
        return $this->scopeConfig->getValue(self::XML_PATH_EVENT_SERVER_URL, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getEventServerPort() {
        return $this->scopeConfig->getValue(self::XML_PATH_EVENT_SERVER_PORT, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function getProductCount() {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_COUNT, $this->_storeScope);
    }


}