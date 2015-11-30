<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClientInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;


class Client implements EngineClientInterface
{
    protected $_factory;

    protected $_logger;

    protected $_config;

    protected $_urls;

    protected $_engineClient;

    public function __construct(
        Factory $factory,
        PersonalisedProductsLogger $logger,
        Config $config,
        Urls $urls
    )
    {
        $this->_factory = $factory;
        $this->_logger = $logger;
        $this->_config = $config;
        $this->_urls = $urls;

        $this->_engineClient = $this->_factory->create(
            'engine',
            $this->_urls->buildUrl(
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_URL),
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_ENGINE_PORT)
            )
        );

    }

    public function sendQuery($customerId)
    {
        try {
            return $this->_engineClient->sendQuery(
                array(
                    'user'=> $customerId,
                    'num'=> (int) $this->_config->getProductCount()
                )
            );
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;

    }
}