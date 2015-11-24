<?php

namespace Richdynamix\PersonalisedProducts\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface as ScopeInterface;

/**
 * Class Config
 * @package Richdynamix\PersonalisedProducts\Helper
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

    const ENABLED = 'personalised_products/general/enabled';
    const ACCESS_KEY = 'personalised_products/general/access_key';
    const PRODUCT_COUNT = 'personalised_products/general/product_count';
    const UPSELL_TEMPLATE_ENGINE_URL = 'personalised_products/upsell_template/engine_url';
    const UPSELL_TEMPLATE_ENGINE_PORT = 'personalised_products/upsell_template/engine_port';
    const UPSELL_TEMPLATE_SERVER_URL = 'personalised_products/upsell_template/event_url';
    const UPSELL_TEMPLATE_SERVER_PORT = 'personalised_products/upsell_template/event_port';
    const RANKING_TEMPLATE_ENGINE_URL = 'personalised_products/product_ranking/engine_url';
    const RANKING_TEMPLATE_ENGINE_PORT = 'personalised_products/product_ranking/engine_port';
    const RANKING_TEMPLATE_SERVER_URL = 'personalised_products/product_ranking/event_url';
    const RANKING_TEMPLATE_SERVER_PORT = 'personalised_products/product_ranking/event_port';

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
     * @param $itemPath
     * @return mixed
     */
    public function getConfigItem($itemPath)
    {
        return $this->scopeConfig->getValue($itemPath, $this->_storeScope);
    }

    /**
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->getConfigItem(self::ENABLED);
    }
}