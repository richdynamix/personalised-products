<?php

namespace Richdynamix\PersonalisedProducts\Controller\Index;

use \Magento\Framework\Session\SessionManager;

class Index extends \Magento\Framework\App\Action\Action {

    protected $resultPageFactory;

    protected $_sessionManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManager $sessionManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_sessionManager = $sessionManager;
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $customer = $this->_customerFactory->create();
        $collection = $customer->getCollection();
//            ->addFieldToSelect('entity_id')
//            ->getItems();
//            ->addFieldToFilter('entity_id', 2)

//            ->addAttributeToSelect('name')
//            ->addAttributeToFilter('sku', ['eq' => 'st1'])
//            ->loadData(true);

        var_dump($collection->getAllIds());

    }
}