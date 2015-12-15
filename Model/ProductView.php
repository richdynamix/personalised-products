<?php

namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Richdynamix\PersonalisedProducts\Model\Frontend\GuestCustomers;

/**
 * Class ProductView
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class ProductView
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
     * @var GuestCustomers
     */
    private $_guestCustomers;

    /**
     * ProductView constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param Client $eventClient
     * @param GuestCustomers $guestCustomers
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Client $eventClient,
        GuestCustomers $guestCustomers
    ) {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
        $this->_guestCustomers = $guestCustomers;
    }

    /**
     * @param $productId
     * @return bool|void
     */
    public function processViews($productId)
    {
        if (!$this->_config->isEnabled()) {
            return false;
        }

        if ($this->_customerSession->isLoggedIn()) {
            return $this->_eventClient->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $productId
            );
        }

        return $this->_guestCustomers->setGuestCustomerProductView($productId);
    }
}
