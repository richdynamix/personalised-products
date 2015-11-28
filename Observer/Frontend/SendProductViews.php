<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServer;
use \Richdynamix\PersonalisedProducts\Model\Frontend\GuestCustomers;

/**
 * Listen for product view events and log the customer actions in the PredictionIO
 * server if the customer is logged in. If the customer is a guest then we record the
 * product views in the session to process later.
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SendProductViews implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var EventServer
     */
    protected $_eventServer;

    /**
     * @var GuestCustomers
     */
    protected $_guestCustomers;

    /**
     * SendProductViews constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param EventServer $eventServer
     * @param GuestCustomers $guestCustomers
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        EventServer $eventServer,
        GuestCustomers $guestCustomers
    )
    {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_eventServer = $eventServer;
        $this->_guestCustomers = $guestCustomers;
    }

    /**
     * @param Observer $observer
     * @return null
     */
    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return;
        }

        $product = $observer->getProduct();
        if ($this->_customerSession->isLoggedIn()) {
            $this->_eventServer->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $product->getId()
            );
            return;
        }

        $this->_guestCustomers->setGuestCustomerProductView($product->getId());

    }
}
