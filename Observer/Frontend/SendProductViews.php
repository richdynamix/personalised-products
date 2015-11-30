<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Richdynamix\PersonalisedProducts\Model\Frontend\GuestCustomers;

/**
 * Listen for product view events and log the customer actions in the PredictionIO
 * server if the customer is logged in. If the customer is a guest then we record the
 * product views in the session to process later.
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendProductViews implements ObserverInterface
{
    protected $_config;

    protected $_customerSession;

    protected $_eventClient;

    protected $_guestCustomers;

    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Client $eventClient,
        GuestCustomers $guestCustomers
    )
    {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
        $this->_guestCustomers = $guestCustomers;
    }

    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return;
        }

        $product = $observer->getProduct();
        if ($this->_customerSession->isLoggedIn()) {
            $this->_eventClient->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $product->getId()
            );
            return;
        }

        $this->_guestCustomers->setGuestCustomerProductView($product->getId());

    }
}
