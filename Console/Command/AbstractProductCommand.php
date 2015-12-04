<?php

namespace Richdynamix\PersonalisedProducts\Console\Command;

use \Symfony\Component\Console\Command\Command;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\State as AppState;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Symfony\Component\Config\Definition\Exception\Exception;
use \Richdynamix\PersonalisedProducts\Model\Export;
use \Richdynamix\PersonalisedProducts\Model\ExportFactory;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;

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
    private $_productFactory;

    /**
     * @var Client
     */
    private $_eventClient;

    /**
     * @var Export
     */
    private $_export;

    /**
     * @var ExportFactory
     */
    private $_exportFactory;

    /**
     * @var PersonalisedProductsLogger
     */
    private $_logger;

    /**
     * AbstractProductCommand constructor.
     * @param ProductFactory $productFactory
     * @param Client $eventClient
     * @param AppState $appState
     */
    public function __construct(
        ProductFactory $productFactory,
        Client $eventClient,
        Export $export,
        ExportFactory $exportFactory,
        AppState $appState,
        PersonalisedProductsLogger $logger
    )
    {
        $this->_productFactory = $productFactory;
        $this->_eventClient = $eventClient;
        $this->_export = $export;
        $this->_exportFactory = $exportFactory;
        $this->_logger = $logger;
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
            $sentProduct = $this->_sendToPredictionIO($productId);
            $exportItem = $this->_export->saveProductForExport($productId);
            $this->_setProductExported($exportItem->getId());

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
    private function _getProductCategoryCollection($productId)
    {
        $product = $this->_productFactory->create();
        $product->load($productId);

        return $product->getCategoryIds();
    }


    /**
     * Send the new product to PredictionIO
     *
     * @param $productId
     * @return bool
     */
    private function _sendToPredictionIO($productId)
    {
        return $this->_eventClient->saveProductData(
            $productId,
            $this->_getProductCategoryCollection($productId)
        );
    }

    /**
     * Mark product as exported in DB
     *
     * @param $exportId
     */
    private function _setProductExported($exportId)
    {
        $export = $this->_exportFactory->create()->load($exportId);
        $export->setData('is_exported', '1');
        try {
            $export->save();
        } catch(\Exception $e) {
            $this->_logger->addCritical($e->getMessage());
        }
    }

}
