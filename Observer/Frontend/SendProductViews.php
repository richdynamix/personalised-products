<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;

/**
 * Listen for product view events and log the customer actions in the PredictionIO
 * server if the customer is logged in. If the customer is a guest then we record the
 * product views in the session to process later.
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendProductViews implements ObserverInterface
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var Client
     */
    private $_eventClient;

    /**
     * SendProductViews constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param Client $eventClient
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Client $eventClient
    ) {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
    }

    /**
     * Record the logged in customers product viewing actions to PredictionIO
     *
     * @param Observer $observer
     * @return bool
     */
    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return false;
        }

        $product = $observer->getProduct();
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_eventClient->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $product->getId()
            );
        }
    }
}
