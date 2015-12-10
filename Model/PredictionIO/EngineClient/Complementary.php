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
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Complementary implements EngineInterface
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
    ) {
        $this->factory = $factory;
        $this->logger = $logger;
        $this->config = $config;
        $this->urls = $urls;

        $this->engineClient = $this->factory->create(
            'engine',
            $this->urls->buildUrl(
                $this->config->getItem(Config::COMPLEMENTARY_ENGINE_URL),
                $this->config->getItem(Config::COMPLEMENTARY_ENGINE_PORT)
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
                'num' => (int) $this->config->getProductCount(Config::COMPLEMENTARY_PRODUCT_COUNT),
            ];

            return $this->engineClient->sendQuery($data);
        } catch (\Exception $e) {
            $this->logger->addCritical($e);
        }

        return false;
    }
}
