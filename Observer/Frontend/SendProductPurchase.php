<?php

namespace Richdynamix\PersonalisedProducts\Observer\Frontend;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\Factory;

/**
 * Listen for sales order event and record the customer-buy-product action in
 * PredictionIO.
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SendProductPurchase implements ObserverInterface
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
     * SendProductViews constructor.
     * @param Config $config
     * @param CustomerSession $customerSession
     * @param Factory $predictionIOFactory
     */
    public function __construct(
        Config $config,
        CustomerSession $customerSession,
        Factory $predictionIOFactory
    )
    {
        $this->_config = $config;
        $this->_customerSession = $customerSession;
        $this->_predictionIOFactory = $predictionIOFactory;
    }

    public function execute(Observer $observer)
    {
        if (!$this->_config->isEnabled()) {
            return;
        }

        $order = $observer->getOrder();
        $productCollection = $order->getItemsCollection();

        if ($this->_customerSession->isLoggedIn()) {
            $this->_sendPurchaseEvent($productCollection);
            return;
        }
    }

    private function _sendPurchaseEvent($productCollection)
    {
        foreach ($productCollection as $product) {
            $eventServer = $this->_predictionIOFactory->create('event');
            $eventServer->createEvent(array(
                'event' => 'buy',
                'entityType' => 'user',
                'entityId' => $this->_customerSession->getCustomerId(),
                'targetEntityType' => 'item',
                'targetEntityId' => $product->getId()
            ));
        }
    }
}
