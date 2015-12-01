<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\State as AppState;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

abstract class AbstractProductCommand extends Command
{

    const CATALOG_SEARCH_VISIBILITY = 4;

    protected $_productFactory;

    protected $_eventClient;

    public function __construct(ProductFactory $productFactory, Client $eventClient, AppState $appState)
    {
        $this->_productFactory = $productFactory;
        $this->_eventClient = $eventClient;
        try {
            $appState->setAreaCode('adminhtml');
        } catch (\Exception $exception) {};
        parent::__construct();
    }

    protected function _sendProductData($collection)
    {
        $collectionCount = count($collection);
        $sentProductCount = 0;
        foreach ($collection as $productId) {
            $sentProduct = $this->_eventClient->saveProductData(
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
        $product = $this->_productFactory->create();
        $product->load($productId);

        return $product->getCategoryIds();
    }

}
