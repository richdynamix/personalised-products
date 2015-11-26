<?php

namespace Richdynamix\HelloWorld\Controller\Ajax;

use \Magento\Framework\App\Action\Context as Context;
use \Richdynamix\HelloWorld\Model\ProductCollection as ProductCollection;


/**
 * Class Index
 * @package Richdynamix\HelloWorld\Controller\Ajax
 */
class Index extends \Magento\Framework\App\Action\Action {

    /**
     * @var ProductCollection
     */
    protected $_productCollection;

    protected $_productFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param ProductCollection $productCollection
     */
    public function __construct(
        Context $context,
        ProductCollection $productCollection,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        parent::__construct($context);
        $this->_productCollection = $productCollection;
        $this->_productFactory = $productFactory;
    }

    public function execute()
    {
//        $data = $this->_productCollection->getCollection();

        $product = $this->_productFactory->create();
        $collection = $product->getCollection()
                    ->addAttributeToSelect('name')
                    ->addAttributeToFilter('sku', ['eq' => 'st1'])
                    ->loadData(true);

        echo $collection->getSelect()->__toString();
//        $product->getData();

//        var_dump($product);

        exit;

//        $jsonData = json_encode(['success' => true, 'data' => $data]);
//        $this->getResponse()->setHeader('Content-type', 'application/json');
//        $this->getResponse()->setBody($jsonData);
    }
}