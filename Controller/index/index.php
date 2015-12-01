<?php

namespace Richdynamix\PersonalisedProducts\Controller\Index;

use \Magento\Framework\Session\SessionManager;
use \Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList\Crosssell as PersonalisedCrosssell;
use \Magento\Customer\Model\Session as CustomerSession;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    protected $_sessionManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManager $sessionManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        CustomerSession $customerSession,
        PersonalisedCrosssell $crosssell
    )
    {
        $this->_sessionManager = $sessionManager;
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        $this->_orderFactory = $orderFactory;
        $this->_crosssell = $crosssell;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {

//        $order = $this->_orderFactory->create();
//        $ordersCollection = $order->getCollection()
//            ->addFieldToSelect(['entity_id', 'customer_id'])
//            ->addFieldToFilter('state', ['eq' => 'complete'])
//            ->addFieldToFilter('customer_id', array('neq' => 'NULL' ))
//            ->getData();
////        ->loadData(true);
//
//        print_r($ordersCollection);
//
//        $purchasedProducts = [];
//        foreach ($ordersCollection as $order) {
//
//            $order = $this->_orderFactory->create()->load($order['entity_id']);
//
//            $itemCollection = $order->getItemsCollection();
//
//            foreach ($itemCollection as $item) {
//                $purchasedProducts[$order['customer_id']][] = $item->getId();
//            }
//
//        }
//
//        print_r($purchasedProducts);
//
//        exit;

        $personalisedIds = $this->_crosssell->getProductCollection(['1']);

        var_dump($personalisedIds);




    }
}