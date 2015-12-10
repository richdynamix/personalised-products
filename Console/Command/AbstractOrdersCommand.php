<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Sales\Model\OrderFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class AbstractOrdersCommand
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
abstract class AbstractOrdersCommand extends Command
{

    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var Client
     */
    private $eventClient;

    /**
     * @var
     */
    private $productCollection;

    /**
     * AbstractOrdersCommand constructor.
     * @param OrderFactory $orderFactory
     * @param Client $eventClient
     */
    public function __construct(OrderFactory $orderFactory, Client $eventClient)
    {
        $this->orderFactory = $orderFactory;
        $this->eventClient = $eventClient;
        parent::__construct();
    }

    /**
     * Prepare each order for customer and product data to send to PredictionIO
     *
     * @param $collection
     * @return int
     */
    protected function sendCustomerBuyProductData($collection)
    {
        $collectionCount = count($collection);
        $sentEventCount = 0;

        foreach ($collection as $customerId => $products) {
            $sentEvent = $this->sendPurchaseEvent($customerId, $products);
            if ($sentEvent) {
                ++$sentEventCount;
            }
        }

        if ($collectionCount != $sentEventCount) {
            throw new Exception('There was a problem sending the event data, check the log file for more information');
        }

        return $sentEventCount;
    }

    /**
     * Get a collection of all completed orders from logged in customers
     *
     * @return array
     */
    protected function getOrderCollection()
    {
        $order = $this->orderFactory->create();
        $ordersCollection = $order->getCollection()
            ->addFieldToSelect(['entity_id', 'customer_id'])
            ->addFieldToFilter('state', ['eq' => 'complete'])
            ->addFieldToFilter('customer_id', array('neq' => 'NULL' ));

        return $ordersCollection->getData();
    }

    /**
     * Get a collection of all products in the orders
     *
     * @param $ordersCollection
     * @return array
     */
    protected function getCustomerProductCollection($ordersCollection)
    {
        $purchasedProducts = [];
        foreach ($ordersCollection as $order) {
            $order = $this->orderFactory->create()->load($order['entity_id']);
            $itemCollection = $order->getItemsCollection();

            foreach ($itemCollection as $item) {
                $purchasedProducts[$order['customer_id']][] = $item->getId();
            }
        }

        $this->productCollection = $purchasedProducts;

        return $purchasedProducts;
    }

    /**
     * Count the number of customers in all the orders
     *
     * @return int
     */
    protected function getCustomerCount()
    {
        return count($this->productCollection);
    }

    /**
     * Count all the products across all the orders
     *
     * @return int
     */
    protected function getProductCount()
    {
        $productCount = 0;
        foreach ($this->productCollection as $products) {
            $productCount += count($products);
        }

        return $productCount;
    }

    /**
     * Send customer-buys-item events to PredictionIO for all existing orders
     *
     * @param $customerId
     * @param $products
     * @return bool
     */
    protected function sendPurchaseEvent($customerId, $products)
    {
        foreach ($products as $productId) {
            if (!$this->eventClient->saveCustomerBuyProduct($customerId, $productId)) {
                return false;
            }
        }

        return true;
    }
}
