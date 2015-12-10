<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Framework\Session\SessionManager;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Magento\Customer\Model\Session as CustomerSession;

/**
 * Class SendGuestActionsCustomLogin listens to customer logins and records the customer
 * to PredictionIO. We then check if the customer viewed any products before logging in
 * and then record these actions also.
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendGuestActionsCustomLogin implements ObserverInterface
{
    /**
     * @var Config
     */
    private $_config;

    /**
     * @var SessionManager
     */
    private $_sessionManager;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var Client
     */
    private $_eventClient;

    /**
     * SendGuestActionsCustomLogin constructor.
     * @param Config $config
     * @param SessionManager $sessionManager
     * @param Client $eventClient
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Config $config,
        SessionManager $sessionManager,
        Client $eventClient,
        CustomerSession $customerSession
    )
    {
        $this->_config = $config;
        $this->_sessionManager = $sessionManager;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
    }

    /**
     * Check on customer login if they have any product view to capture
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->_config->isEnabled()) {
            $this->_eventClient->saveCustomerData($this->_customerSession->getCustomerId());

            $guestProductViews = $this->_sessionManager->getGuestProductViews();
            if ($guestProductViews) {
                $this->_sendAllGuestProductViews($guestProductViews);
            }
        }
    }

    /**
     * Send all the guest product views to PredictionIO when we get the customers ID
     *
     * @param $guestProductViews
     */
    private function _sendAllGuestProductViews($guestProductViews)
    {
        foreach ($guestProductViews as $productId) {
            $this->_eventClient->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $productId
            );
        }

        $this->_sessionManager->setGuestProductViews(null);
    }

}
