<?php

namespace Richdynamix\PersonalisedProducts\Model\PredictionIO;

use \Richdynamix\PersonalisedProducts\Helper\Config;
use Richdynamix\PersonalisedProducts\Helper\Urls;
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

    protected $_config;

    protected $_urls;

    protected $_eventServer;

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

        $this->_eventServer = $this->_factory->create(
            'event',
            $this->_urls->buildUrl(
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_URL),
                $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_PORT)
            ),
            $this->_config->getConfigItem(Config::UPSELL_TEMPLATE_SERVER_ACCESS_KEY)
        );

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
        //todo add unavailable items to event server
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
            $this->_eventServer->createEvent(array(
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
            $this->_eventServer->createEvent($data);
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
