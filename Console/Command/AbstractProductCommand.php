<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\State as AppState;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class AbstractProductCommand
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
abstract class AbstractProductCommand extends Command
{

    /**
     * Product visibility value
     */
    const CATALOG_SEARCH_VISIBILITY = 4;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Client
     */
    protected $_eventClient;

    /**
     * AbstractProductCommand constructor.
     * @param ProductFactory $productFactory
     * @param Client $eventClient
     * @param AppState $appState
     */
    public function __construct(ProductFactory $productFactory, Client $eventClient, AppState $appState)
    {
        $this->_productFactory = $productFactory;
        $this->_eventClient = $eventClient;
        try {
            $appState->setAreaCode('adminhtml');
        } catch (\Exception $exception) {};
        parent::__construct();
    }

    /**
     * Send product data to PredictionIO
     *
     * @param $collection
     * @return int
     */
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

    /**
     * Get product collection
     *
     * @return array
     */
    protected function _getProductCollection()
    {
        $product = $this->_productFactory->create();
        $collection = $product->getCollection()
            ->addAttributeToFilter('visibility', self::CATALOG_SEARCH_VISIBILITY);

        return $collection->getAllIds();
    }

    /**
     * Get category collection for each product
     *
     * @param $productId
     * @return array
     */
    protected function _getProductCategoryCollection($productId)
    {
        $product = $this->_productFactory->create();
        $product->load($productId);

        return $product->getCategoryIds();
    }

}
