<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient;

use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Api\Data\EventClientInterface;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;

/**
 * Class EventClient for sending date and actions to PredictionIO
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Client implements EventClientInterface
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
     * @var null|\predictionio\EngineClient|\predictionio\EventClient
     */
    private $_eventClient;

    /**
     * Client constructor.
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

        $this->_eventClient = $this->_factory->create(
            'event',
            $this->_urls->buildUrl(
                $this->_config->getItem(Config::EVENT_SERVER_URL),
                $this->_config->getItem(Config::EVENT_SERVER_PORT)
            ),
            $this->_config->getItem(Config::EVENT_SERVER_ACCESS_KEY)
        );

    }

    /**
     * Send customer data to PredictionIO
     *
     * @param int $customerId
     * @return bool
     */
    public function saveCustomerData($customerId)
    {
        return $this->_setEntity('user', $customerId);
    }

    /**
     * Send product data to PredictionIO
     *
     * @param int $productId
     * @param array $categoryIds
     * @return bool
     */
    public function saveProductData($productId, array $categoryIds = [])
    {
        return $this->_setEntity('item', $productId, $categoryIds);
    }

    /**
     * Send customer-views-product event to PredictionIO
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function saveCustomerViewProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('view', $customerId, $productId);
    }

    /**
     * Send customer-buys-product event to PredictionIO
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function saveCustomerBuyProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('buy', $customerId, $productId);
    }

    /**
     * Method for sending user-action-item events
     *
     * @param $action
     * @param $customerId
     * @param $productId
     * @return bool
     */
    private function _setCustomerToItemAction($action, $customerId, $productId)
    {
        try {
            $this->_eventClient->createEvent(array(
                'event' => $action,
                'entityType' => 'user',
                'entityId' => $customerId,
                'targetEntityType' => 'item',
                'targetEntityId' => $productId
            ));

            return true;
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;

    }

    /**
     * Method to send individual entities
     *
     * @param $entityType
     * @param $entityId
     * @param null $properties
     * @return bool
     */
    private function _setEntity($entityType, $entityId, $properties = null)
    {
        try {
            $data = $this->_addProperties(
                [
                    'event' => '$set',
                    'entityType' => $entityType,
                    'entityId' => $entityId
                ],
                $properties
            );
            $this->_eventClient->createEvent($data);
            return true;
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;
    }

    /**
     * Add properties to query
     *
     * @param $data
     * @param $properties
     * @return mixed
     */
    private function _addProperties($data, $properties)
    {
        if (null !== $properties) {
            $data['properties'] = ['categories' => $properties];
        }

        return $data;
    }
}
