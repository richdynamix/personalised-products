<?php

namespace Richdynamix\PersonalisedProducts\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface as ScopeInterface;
use \Magento\Framework\App\Helper\Context as Context;

/**
 * Configuration class to retrieve module options from the database
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @var ScopeInterface
     */
    private $_storeScope;

    /**
     * Is the module enabled
     */
    const ENABLED = 'personalised_products/general/enabled';

    /**
     * The event server access key
     */
    const EVENT_SERVER_ACCESS_KEY = 'personalised_products/general/access_key';

    /**
     * The event server URL
     */
    const EVENT_SERVER_URL = 'personalised_products/general/url';

    /**
     * The event server port
     */
    const EVENT_SERVER_PORT = 'personalised_products/general/port';

    /**
     * Similarity engine URL (Upsell engine)
     */
    const SIMILARITY_ENGINE_URL = 'personalised_products/similarity_engine/url';

    /**
     * Similarity engine port (Upsell engine)
     */
    const SIMILARITY_ENGINE_PORT = 'personalised_products/similarity_engine/port';

    /**
     * Similarity engine products to return count
     */
    const SIMILARITY_PRODUCT_COUNT = 'personalised_products/similarity_engine/product_count';

    /**
     * Similarity engine category filter check
     */
    const SIMILARITY_USE_CATEGORY_FILTER = 'personalised_products/similarity_engine/use_categories';

    /**
     * Switch to replace Rule Based Upsells (enterprise edition only)
     */
    const SIMILARITY_REPLACE_RULES = 'personalised_products/similarity_engine/replace_rules';

    /**
     * Complementary engine URL (Crosssell engine)
     */
    const COMPLEMENTARY_ENGINE_URL = 'personalised_products/complementary_engine/url';

    /**
     * Complementary engine port (Crosssell engine)
     */
    const COMPLEMENTARY_ENGINE_PORT = 'personalised_products/complementary_engine/port';

    /**
     * Complementary engine product to return count
     */
    const COMPLEMENTARY_PRODUCT_COUNT = 'personalised_products/complementary_engine/product_count';

    /**
     * Switch to replace Rule Based Cross sells (enterprise edition only)
     */
    const COMPLEMENTARY_REPLACE_RULES = 'personalised_products/complementary_engine/replace_rules';

    /**
     * Config constructor
     * @param Context $context
     */
    public function __construct(ScopeConfigInterface $scopeConfig, Context $context)
    {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        parent::__construct($context);
    }

    /**
     * Global method for getting configuration value
     *
     * @param $itemPath
     * @return mixed
     */
    public function getItem($itemPath)
    {
        return $this->scopeConfig->getValue($itemPath, $this->_storeScope);
    }

    /**
     * Check the module is enabled
     *
     * @return mixed
     */
    public function isEnabled()
    {
        return $this->getItem(self::ENABLED);
    }

    /**
     * Get a product count for the engine (default to 4)
     *
     * @param $engineProductCount
     * @return mixed|string
     */
    public function getProductCount($engineProductCount)
    {
        return $this->getItem($engineProductCount) ? $this->getItem($engineProductCount) : '4';
    }
}
