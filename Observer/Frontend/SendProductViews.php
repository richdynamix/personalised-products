<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;
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
     * @var Factory
     */
    protected $_predictionIOFactory;

    /**
     * @var GuestCustomers
     */
    protected $_guestCustomers;

    /**
     * SendProductViews constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param Factory $predictionIOFactory
     * @param GuestCustomers $guestCustomers
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Factory $predictionIOFactory,
        GuestCustomers $guestCustomers
    )
    {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_predictionIOFactory = $predictionIOFactory;
        $this->_guestCustomers = $guestCustomers;
    }

    /**
     * @param Observer $observer
     * @return null
     */
    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return null;
        }

        $product = $observer->getProduct();

        if ($this->_customerSession->isLoggedIn()) {

            $eventServer = $this->_predictionIOFactory->create('event');
            $eventServer->createEvent(array(
                'event' => 'view',
                'entityType' => 'user',
                'entityId' => $this->_customerSession->getCustomerId(),
                'targetEntityType' => 'item',
                'targetEntityId' => $product->getId()
            ));

        } else {
            $this->_guestCustomers->setGuestCustomerProductView($product);
        }

    }
}
