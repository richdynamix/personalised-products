<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

use \Richdynamix\PersonalisedProducts\Api\Data\EngineInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;

/**
 * Class Similarity
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Similarity implements EngineInterface
{
    /**
     * @var Factory
     */
    private $_factory;

    /**
     * @var PersonalisedProductsLogger
     */
    private $_logger;

    /**
     * @var Config
     */
    private $_config;

    /**
     * @var Urls
     */
    private $_urls;

    /**
     * @var \predictionio\EngineClient
     */
    private $_engineClient;

    /**
     * Similarity constructor.
     * @param Factory $factory
     * @param PersonalisedProductsLogger $logger
     * @param Config $config
     * @param Urls $urls
     */
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

    /**
     * Send the query to PredictionIO engine for product data set
     *
     * @param array $productIds
     * @param array $categories
     * @param array $whitelist
     * @param array $blacklist
     * @return array|bool
     */
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

    /**
     * Add query properties to the data in the query for PredictionIO engine
     *
     * @param $property
     * @param $data
     * @param $propertyData
     * @return $this
     */
    private function _addProperties($property, &$data, $propertyData)
    {
        if ($propertyData) {
            $data[$property] = $propertyData;
        }

        return $this;
    }
}
