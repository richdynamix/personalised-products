<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\SimilarityInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;


class Similarity implements SimilarityInterface
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
                $this->_config->getItem(Config::SIMILARITY_ENGINE_URL),
                $this->_config->getItem(Config::SIMILARITY_ENGINE_PORT)
            )
        );

    }

    public function sendQuery(array $productIds, array $categories = [], array $whitelist = [], array $blacklist = [])
    {
        try {
            $data = [

              'items' => $productIds,
              'num' => (int) $this->_config->getProductCount(Config::SIMILARITY_PRODUCT_COUNT),
            ];

            $this->_addProperties('categories', $data, $categories);
            $this->_addProperties('whitelist', $data, $whitelist);
            $this->_addProperties('blacklist', $data, $blacklist);

            return $this->_engineClient->sendQuery($data);
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;

    }

    protected function _addProperties($property, &$data, $propertyData)
    {
        if ($propertyData) {
            $data[$property] = $propertyData;
        }

        return $this;
    }
}