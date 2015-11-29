<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Catalog\Model\ProductFactory;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventServer;
use \Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractProductCommand extends Command
{

    CONST CATALOG_SEARCH_VISIBILITY = 4;

    protected $_productFactory;

    protected $_eventServer;

    public function __construct(ProductFactory $productFactory, EventServer $eventServer)
    {
        $this->_productFactory = $productFactory;
        $this->_eventServer = $eventServer;
        parent::__construct();
    }

    protected function _sendProductData($collection)
    {
        $collectionCount = count($collection);
        $sentProductCount = 0;
        foreach ($collection as $productId) {

            $sentProduct = $this->_eventServer->saveProductData(
                $productId,
                $this->_getProductCategoryCollection($productId)
            );

            if ($sentProduct) {
                ++$sentProductCount;
            }
        }

        if ($collectionCount != $sentProductCount) {
            throw new Exception('There was a problem sending the product data, check the log file for more information');
        }

        return $sentProductCount;

    }

    protected function _getProductCollection()
    {
        $product = $this->_productFactory->create();
        $collection = $product->getCollection()
            ->addAttributeToFilter('visibility', self::CATALOG_SEARCH_VISIBILITY);

        return $collection->getAllIds();
    }

    protected function _getProductCategoryCollection($productId)
    {
        // todo fix issue with session area not being set when filtering categories
//        $product = $this->_productFactory->create()->load($productId);
//        return $product->getCategoryIds();
        return [];
    }

}
