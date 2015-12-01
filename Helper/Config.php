<?php

namespace Richdynamix\PersonalisedProducts\Helper;

use \Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface as ScopeInterface;
use \Magento\Framework\App\Helper\Context as Context;

/**
 * Configuration class to retrieve module options from the database
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    const EVENT_SERVER_ACCESS_KEY = 'personalised_products/general/access_key';

    const EVENT_SERVER_URL = 'personalised_products/general/url';

    const EVENT_SERVER_PORT = 'personalised_products/general/port';

    const SIMILARITY_ENGINE_URL = 'personalised_products/similarity_engine/url';

    const SIMILARITY_ENGINE_PORT = 'personalised_products/similarity_engine/port';

    const SIMILARITY_PRODUCT_COUNT = 'personalised_products/similarity_engine/product_count';

    const SIMILARITY_USE_CATEGORY_FILTER = 'personalised_products/similarity_engine/use_categories';

    const COMPLEMENTARY_ENGINE_URL = 'personalised_products/complementary_engine/url';

    const COMPLEMENTARY_ENGINE_PORT = 'personalised_products/complementary_engine/port';

    const COMPLEMENTARY_PRODUCT_COUNT = 'personalised_products/complementary_engine/product_count';

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     */
    public function __construct(ScopeConfigInterface $scopeConfig, Context $context)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        parent::__construct($context);
    }

    public function getItem($itemPath)
    {
        return $this->scopeConfig->getValue($itemPath, $this->_storeScope);
    }

    public function isEnabled()
    {
        return $this->getItem(self::ENABLED);
    }

    public function getProductCount($engineProductCount)
    {
        return $this->getItem($engineProductCount) ? $this->getItem($engineProductCount) : '4';
    }
}
