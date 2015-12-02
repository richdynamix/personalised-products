<?php

namespace Richdynamix\PersonalisedProducts\Controller\Index;

use \Magento\Framework\Session\SessionManager;
use \Magento\Customer\Model\Session as CustomerSession;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\Collection as ExportCollection;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\CollectionFactory;
use \Richdynamix\PersonalisedProducts\Api\Data\ExportInterface;

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
        ExportCollection $exportCollection,
        CollectionFactory $exportCollectionFactory
    )
    {
        $this->_sessionManager = $sessionManager;
        $this->_productFactory = $productFactory;
        $this->_customerFactory = $customerFactory;
        $this->_orderFactory = $orderFactory;
        $this->_customerSession = $customerSession;
        $this->_exportCollection = $exportCollection;
        $this->_exportCollectionFactory = $exportCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {

//        $exports = $this->_exportCollectionFactory
//            ->create()
//            ->addOrder(
//                ExportInterface::CREATION_TIME,
//                ExportCollection::SORT_ORDER_DESC
//            );

        $exports = $this->_exportCollectionFactory
            ->create()
            ->addFieldToFilter('is_exported', '0')
            ->addOrder(
                ExportInterface::CREATION_TIME,
                ExportCollection::SORT_ORDER_DESC
            );


        echo $exports->getSelect()->__toString();

//        var_dump($exports->getAllIds());

        $products = [];
        foreach ($exports as $export) {

            $products[$export->getProductId()] = $export->getCategoryIds();
//            var_dump($export->getProductId());
        }

        var_dump($products);
    }


}