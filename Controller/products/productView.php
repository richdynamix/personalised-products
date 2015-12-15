<?php

namespace Richdynamix\PersonalisedProducts\Controller\Products;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\Session\SessionManager;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\App\Action\Context;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Richdynamix\PersonalisedProducts\Model\Frontend\GuestCustomers;
use \Richdynamix\PersonalisedProducts\Model\ProductView as ProductViewModel;

/**
 * Class ProductView
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class ProductView extends Action {

    /**
     * @var SessionManager
     */
    protected $_sessionManager;

    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * ProductView constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param SessionManager $sessionManager
     * @param CustomerSession $customerSession
     * @param Client $eventClient
     * @param GuestCustomers $guestCustomers
     * @param ProductViewModel $productView
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SessionManager $sessionManager,
        CustomerSession $customerSession,
        Client $eventClient,
        GuestCustomers $guestCustomers,
        ProductViewModel $productView
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_sessionManager = $sessionManager;
        $this->_customerSession = $customerSession;
        $this->_eventClient = $eventClient;
        $this->_guestCustomers = $guestCustomers;
        $this->_productView = $productView;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->_sessionManager->start();

        $result = $this->_resultJsonFactory->create();

        $productId = $this->getRequest()->getParam('id');
        if (empty($productId)) {
            return $result->setData(['error' => true, 'message' => 'Product ID has not been supplied']);
        }

        $pageViewResult = $this->_productView->processViews($productId);

        if (!$pageViewResult) {
            return $result->setData(['error' => true, 'message' => 'There was an error processing the product view.']);
        }

        if (is_array($pageViewResult)) {
            return $result->setData(['success' => true, 'message' => $pageViewResult]);
        }

        return $result->setData(['success' => true, 'message' => 'Product view logged in PredictionIO']);
    }
}
