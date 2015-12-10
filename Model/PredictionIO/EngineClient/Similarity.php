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
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Similarity implements EngineInterface
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var PersonalisedProductsLogger
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Urls
     */
    private $urls;

    /**
     * @var \predictionio\EngineClient
     */
    private $engineClient;

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
    ) {
        $this->factory = $factory;
        $this->logger = $logger;
        $this->config = $config;
        $this->urls = $urls;

        $this->engineClient = $this->factory->create(
            'engine',
            $this->urls->buildUrl(
                $this->config->getItem(Config::SIMILARITY_ENGINE_URL),
                $this->config->getItem(Config::SIMILARITY_ENGINE_PORT)
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
              'num' => (int) $this->config->getProductCount(Config::SIMILARITY_PRODUCT_COUNT),
            ];

            $this->addProperties('categories', $data, $categories);
            $this->addProperties('whitelist', $data, $whitelist);
            $this->addProperties('blacklist', $data, $blacklist);

            return $this->engineClient->sendQuery($data);
        } catch (\Exception $e) {
            $this->logger->addCritical($e);
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
    private function addProperties($property, &$data, $propertyData)
    {
        if ($propertyData) {
            $data[$property] = $propertyData;
        }

        return $this;
    }
}
