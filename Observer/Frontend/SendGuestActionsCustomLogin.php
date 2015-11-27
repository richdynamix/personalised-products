<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;

class SendGuestActionsCustomLogin implements ObserverInterface
{
    protected $_config;

    public function __construct(Config $config)
    {
        $this->_config = $config;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
    }
}
