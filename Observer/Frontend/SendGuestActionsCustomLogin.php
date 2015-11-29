<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Framework\Session\SessionManager;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServer;
use \Magento\Customer\Model\Session as CustomerSession;

/**
 * Class SendGuestActionsCustomLogin listens to customer logins and records the customer
 * to PredictionIO. We then check if the customer viewed any products before logging in
 * and then record these actions also.
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SendGuestActionsCustomLogin implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var SessionManager
     */
    protected $_sessionManager;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var EventServer
     */
    protected $_eventServer;

    /**
     * SendGuestActionsCustomLogin constructor.
     * @param Config $config
     * @param SessionManager $sessionManager
     * @param EventServer $eventServer
     * @param CustomerSession $customerSession
     */
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

    /**
     * @param Observer $observer
     */
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

    /**
     * @param $guestProductViews
     */
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
