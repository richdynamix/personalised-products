<?php

namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;

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
     * ProductView constructor.
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
    }
}
