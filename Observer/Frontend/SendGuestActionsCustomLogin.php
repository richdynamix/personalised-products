<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Framework\Session\SessionManager;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServer;
use \Magento\Customer\Model\Session as CustomerSession;

class SendGuestActionsCustomLogin implements ObserverInterface
{
    protected $_config;

    protected $_sessionManager;

    protected $_customerSession;

    protected $_eventServer;

    public function __construct(
        Config $config,
        SessionManager $sessionManager,
        EventServer $eventServer,
        CustomerSession $customerSession
    )
    {
        $this->_config = $config;
        $this->_sessionManager = $sessionManager;
        $this->_customerSession = $customerSession;
        $this->_eventServer = $eventServer;
    }

    public function execute(Observer $observer)
    {
        if ($this->_config->isEnabled()) {
            $this->_eventServer->saveCustomerData($this->_customerSession->getCustomerId());

            $guestProductViews = $this->_sessionManager->getGuestProductViews();
            if ($guestProductViews) {
                $this->_sendAllGuestProductViews($guestProductViews);
            }
        }
    }

    protected function _sendAllGuestProductViews($guestProductViews)
    {
        foreach ($guestProductViews as $productId) {
            $this->_eventServer->saveCustomerViewProduct(
                $this->_customerSession->getCustomerId(),
                $productId
            );
        }

        $this->_sessionManager->setGuestProductViews(null);
    }

}
