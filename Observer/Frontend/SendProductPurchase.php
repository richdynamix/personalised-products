<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;

/**
 * Listen for sales order event and record the customer-buy-product action in
 * PredictionIO.
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendProductPurchase implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var
     */
    private $eventClient;

    /**
     * SendProductPurchase constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param Client $eventClient
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Client $eventClient
    ) {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->eventClient = $eventClient;
    }

    /**
     * Check the customer has an account and send the order product colllection
     * to PredictionIO
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        $productCollection = $order->getItemsCollection();
        if ($this->customerSession->isLoggedIn()) {
            $this->sendPurchaseEvent($productCollection);
            return;
        }
    }

    /**
     * Record a customer-buys-product event in PredictionIO when the customer
     * completes an order
     *
     * @param $productCollection
     */
    private function sendPurchaseEvent($productCollection)
    {
        foreach ($productCollection as $product) {
            $this->eventClient->saveCustomerBuyProduct(
                $this->customerSession->getCustomerId(),
                $product->getId()
            );
        }

        return;
    }
}
