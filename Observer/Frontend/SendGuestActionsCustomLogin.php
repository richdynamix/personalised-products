<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Framework\Session\SessionManager;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\App\Action\Context;

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
     * @var Context
     */
    private $_context;

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
     * @param Context $context
     * @param Config $config
     * @param SessionManager $sessionManager
     * @param Client $eventClient
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Context $context,
        Config $config,
        SessionManager $sessionManager,
        Client $eventClient,
        CustomerSession $customerSession
    ) {
        $this->_context = $context;
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

            $guestProductViews = $this->_getGuestCustomerProductViews();
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
    }

    /**
     * Get all product ID's that were saved in the cookie during this session.
     *
     * @return array
     */
    private function _getGuestCustomerProductViews()
    {
        $guestProductViews = rtrim($this->_getCookie('productviews'), ',');
        return explode(',', $guestProductViews);
    }

    /**
     * Helper method to read the cookie information
     *
     * @param $name
     * @return null|string
     */
    private function _getCookie($name)
    {
        return $this->_context->getRequest()->getCookie($name, '');
    }
}
