<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServerInterface;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;

/**
 * Class EventServer for sending date and actions to PredictionIO
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class EventServer implements EventServerInterface
{
    /**
     * @var \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory
     */
    protected $_factory;

    /**
     * @var PersonalisedProductsLogger
     */
    protected $_logger;

    /**
     * EventServer constructor.
     * @param \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory $factory
     * @param PersonalisedProductsLogger $logger
     */
    public function __construct(Factory $factory, PersonalisedProductsLogger $logger)
    {
        $this->_factory = $factory;
        $this->_logger = $logger;
    }

    /**
     * @param int $customerId
     * @return bool
     */
    public function saveCustomerData($customerId)
    {
        return $this->_setEntity('user', $customerId);
    }

    /**
     * @param int $productId
     * @param array $categoryIds
     * @return bool
     */
    public function saveProductData($productId, array $categoryIds = [])
    {
        return $this->_setEntity('item', $productId, $categoryIds);
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function saveCustomerViewProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('view', $customerId, $productId);
    }

    /**
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function saveCustomerBuyProduct($customerId, $productId)
    {
        return $this->_setCustomerToItemAction('buy', $customerId, $productId);
    }

    /**
     * @param array $productIds
     */
    public function setOutOfStockItems(array $productIds)
    {
        //todo add unaavailable items to event server
    }

    /**
     * @param $action
     * @param $customerId
     * @param $productId
     * @return bool
     */
    protected function _setCustomerToItemAction($action, $customerId, $productId)
    {
        try {
            $eventServer = $this->_factory->create('event');
            $eventServer->createEvent(array(
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
     * @param $entityType
     * @param $entityId
     * @param null $properties
     * @return bool
     */
    protected function _setEntity($entityType, $entityId, $properties = null)
    {
        try {
            $eventServer = $this->_factory->create('event');

            $data = $this->_addProperties(
                [
                    'event' => '$set',
                    'entityType' => $entityType,
                    'entityId' => $entityId
                ],
                $properties
            );
            $eventServer->createEvent($data);
            return true;
        } catch (\Exception $e) {
            $this->_logger->addCritical($e);
        }

        return false;
    }

    /**
     * @param $data
     * @param $properties
     * @return mixed
     */
    protected function _addProperties($data, $properties)
    {
        if (null !== $properties) {
            $data['properties'] = ['categories' => $properties];
        }

        return $data;
    }
}
