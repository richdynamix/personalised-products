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

        $product = $this->_productFactory->create();
        $collection = $product->getCollection()
            ->addAttributeToFilter('visibility', 4);

        $products = [];
        foreach ($collection->getAllIds() as $productId) {
            $product = $this->_productFactory->create()->load($productId);
            $products[$productId]['categories'] = $product->getCategoryIds();
        }


        foreach ($products as $productId => $attributes) {

            var_dump($productId);
            var_dump($attributes['categories']);
            exit;

        }


//        print_r($products);

        exit;

    }
}