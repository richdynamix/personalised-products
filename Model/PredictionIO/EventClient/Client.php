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
    protected $_factory;

    protected $_logger;

    protected $_config;

    protected $_urls;

    protected $_eventClient;

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

    public function saveCustomerData($customerId)
    {
        return $this->_setEntity('user', $customerId);
    }

    public function saveProductData($productId, array $categoryIds = [])
    {
        return $this->_setEntity('item', $productId, $categoryIds);
    }

    public function saveCustomerViewProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('view', $customerId, $productId);
    }

    public function saveCustomerBuyProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('buy', $customerId, $productId);
    }

    protected function _setCustomerToItemAction($action, $customerId, $productId)
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

    protected function _setEntity($entityType, $entityId, $properties = null)
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

    protected function _addProperties($data, $properties)
    {
        if (null !== $properties) {
            $data['properties'] = ['categories' => $properties];
        }

        return $data;
    }
}
