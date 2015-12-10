<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient;

use \Richdynamix\PersonalisedProducts\Api\Data\EngineInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;

/**
 * Class Complementary
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Complementary implements EngineInterface
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
     * Complementary constructor.
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
                $this->_config->getItem(Config::COMPLEMENTARY_ENGINE_URL),
                $this->_config->getItem(Config::COMPLEMENTARY_ENGINE_PORT)
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
                'num' => (int) $this->_config->getProductCount(Config::COMPLEMENTARY_PRODUCT_COUNT),
            ];

            return $this->_engineClient->sendQuery($data);
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;

    }
}
