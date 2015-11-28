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

    /**
     * On/Off switch for the module
     */
    const ENABLED = 'personalised_products/general/enabled';

    /**
     * Determines the number of products to be returned from PredictionIO
     */
    const PRODUCT_COUNT = 'personalised_products/general/product_count';

    /**
     * UpSell (ecommerce) template engine URL
     */
    const UPSELL_TEMPLATE_ENGINE_URL = 'personalised_products/upsell_template/engine_url';

    /**
     * UpSell (ecommerce) template engine port (default: 7070)
     */
    const UPSELL_TEMPLATE_ENGINE_PORT = 'personalised_products/upsell_template/engine_port';

    /**
     * UpSell (ecommerce) template event server's access key
     */
    const UPSELL_TEMPLATE_SERVER_ACCESS_KEY = 'personalised_products/upsell_template/access_key';

    /**
     * UpSell (ecommerce) template event server URL
     */
    const UPSELL_TEMPLATE_SERVER_URL = 'personalised_products/upsell_template/event_url';

    /**
     * UpSell (ecommerce) template event server port (default: 8000)
     */
    const UPSELL_TEMPLATE_SERVER_PORT = 'personalised_products/upsell_template/event_port';

    /**
     * Product ranking template engine URL
     */
    const RANKING_TEMPLATE_ENGINE_URL = 'personalised_products/product_ranking/engine_url';

    /**
     * Product ranking template engine port (default: 7070)
     */
    const RANKING_TEMPLATE_ENGINE_PORT = 'personalised_products/product_ranking/engine_port';

    /**
     * Product ranking template event server's access key
     */
    const RANKING_TEMPLATE_SERVER_ACCESS_KEY = 'personalised_products/product_ranking/access_key';

    /**
     * Product ranking template event server URL
     */
    const RANKING_TEMPLATE_SERVER_URL = 'personalised_products/product_ranking/event_url';

    /**
     * Product ranking template event server port (default: 8000)
     */
    const RANKING_TEMPLATE_SERVER_PORT = 'personalised_products/product_ranking/event_port';


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
