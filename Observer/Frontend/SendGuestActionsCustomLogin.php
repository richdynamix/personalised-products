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
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class SendGuestActionsCustomLogin implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var Client
     */
    private $eventClient;

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
    ) {
        $this->config = $config;
        $this->sessionManager = $sessionManager;
        $this->customerSession = $customerSession;
        $this->eventClient = $eventClient;
    }

    /**
     * Check on customer login if they have any product view to capture
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $this->eventClient->saveCustomerData($this->customerSession->getCustomerId());

            $guestProductViews = $this->sessionManager->getGuestProductViews();
            if ($guestProductViews) {
                $this->sendAllGuestProductViews($guestProductViews);
            }
        }
    }

    /**
     * Send all the guest product views to PredictionIO when we get the customers ID
     *
     * @param $guestProductViews
     */
    private function sendAllGuestProductViews($guestProductViews)
    {
        foreach ($guestProductViews as $productId) {
            $this->eventClient->saveCustomerViewProduct(
                $this->customerSession->getCustomerId(),
                $productId
            );
        }

        $this->sessionManager->setGuestProductViews(null);
    }
}
