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
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendProductPurchase implements ObserverInterface
{
    protected $_config;

    protected $_customerSession;

    protected $_eventServer;

    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Client $eventClient
    )
    {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
    }

    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        $productCollection = $order->getItemsCollection();
        if ($this->_customerSession->isLoggedIn()) {
            $this->_sendPurchaseEvent($productCollection);
            return;
        }
    }

    private function _sendPurchaseEvent($productCollection)
    {
        foreach ($productCollection as $product) {
            $this->_eventClient->saveCustomerBuyProduct(
                $this->_customerSession->getCustomerId(),
                $product->getId()
            );
        }

        return;
    }
}
