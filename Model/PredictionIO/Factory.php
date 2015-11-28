<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \predictionio\EventClient;
use \predictionio\EngineClient;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Helper\Urls;

/**
 * PredictionIO factory to return ready to use engine or event server objects
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Factory
{

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var Urls
     */
    protected $_urls;

    /**
     * Factory constructor.
     * @param Config $config
     * @param Urls $urls
     */
    public function __construct(Config $config, Urls $urls)
    {
        $this->_config = $config;
        $this->_urls = $urls;
    }

    /**
     * @param $model
     * @return null|EngineClient|EventClient
     */
    public function create($model)
    {
        if ('event' == $model) {
            $eventUrl = $this->_urls->sanatiseUrl(
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_URL),
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_PORT)
            );
            return new EventClient(Config::UPSELL_TEMPLATE_SERVER_ACCESS_KEY, $eventUrl);
        } elseif ('engine' == $model) {
            $engineUrl = $this->_urls->sanatiseUrl(
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_URL),
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_PORT)
            );
            return new EngineClient($engineUrl);
        }

        return null;
    }
}
