<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Sales\Model\OrderFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractOrdersCommand extends Command
{

    protected $_orderFactory;

    protected $_eventClient;

    protected $_productCollection;

    public function __construct(OrderFactory $orderFactory, Client $eventClient)
    {
        $this->_orderFactory = $orderFactory;
        $this->_eventClient = $eventClient;
        parent::__construct();
    }

    protected function _sendCustomerBuyProductData($collection)
    {
        $collectionCount = count($collection);
        $sentEventCount = 0;

        foreach ($collection as $customerId => $products) {
            $sentEvent = $this->_sendPurchaseEvent($customerId, $products);
            if ($sentEvent) {
                ++$sentEventCount;
            }
        }

        if ($collectionCount != $sentEventCount) {
            throw new Exception('There was a problem sending the event data, check the log file for more information');
        }

        return $sentEventCount;

    }

    protected function _getOrderCollection()
    {
        $order = $this->_orderFactory->create();
        $ordersCollection = $order->getCollection()
            ->addFieldToSelect(['entity_id', 'customer_id'])
            ->addFieldToFilter('state', ['eq' => 'complete'])
            ->addFieldToFilter('customer_id', array('neq' => 'NULL' ));

        return $ordersCollection->getData();
    }

    protected function _getCustomerProductCollection($ordersCollection)
    {
        $purchasedProducts = [];
        foreach ($ordersCollection as $order) {
            $order = $this->_orderFactory->create()->load($order['entity_id']);
            $itemCollection = $order->getItemsCollection();

            foreach ($itemCollection as $item) {
                $purchasedProducts[$order['customer_id']][] = $item->getId();
            }
        }

        $this->_productCollection = $purchasedProducts;

        return $purchasedProducts;
    }

    protected function _getCustomerCount()
    {
        return count($this->_productCollection);
    }

    protected function _getProductCount()
    {
        $productCount = 0;
        foreach ($this->_productCollection as $products) {
            $productCount += count($products);
        }

        return $productCount;
    }

    protected function _sendPurchaseEvent($customerId, $products)
    {
        foreach ($products as $productId) {
            if (!$this->_eventClient->saveCustomerBuyProduct($customerId, $productId)) {
                return false;
            }
        }

        return true;
    }
}
