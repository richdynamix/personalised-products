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
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
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
    private $productFactory;

    /**
     * @var Client
     */
    private $eventClient;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var ExportFactory
     */
    private $exportFactory;

    /**
     * @var PersonalisedProductsLogger
     */
    private $logger;

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
    ) {
        $this->productFactory = $productFactory;
        $this->eventClient = $eventClient;
        $this->export = $export;
        $this->exportFactory = $exportFactory;
        $this->logger = $logger;
        try {
            $appState->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            $this->logger->addCritical($e->getMessage());
        };
        parent::__construct();
    }

    /**
     * Send product data to PredictionIO
     *
     * @param $collection
     * @return int
     */
    protected function sendProductData($collection)
    {
        $collectionCount = count($collection);
        $sentProductCount = 0;
        foreach ($collection as $productId) {
            $sentProduct = $this->sendToPredictionIO($productId);
            $exportItem = $this->export->saveProductForExport($productId);
            $this->setProductExported($exportItem->getId());

            if ($sentProduct) {
                ++$sentProductCount;
            }
        }

        if ($collectionCount != $sentProductCount) {
            throw new Exception(
                'There was a problem sending the product data, check the log file for more information'
            );
        }

        return $sentProductCount;

    }

    /**
     * Get product collection
     *
     * @return array
     */
    protected function getProductCollection()
    {
        $product = $this->productFactory->create();
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
    private function getProductCategoryCollection($productId)
    {
        $product = $this->productFactory->create();
        $product->load($productId);

        return $product->getCategoryIds();
    }


    /**
     * Send the new product to PredictionIO
     *
     * @param $productId
     * @return bool
     */
    private function sendToPredictionIO($productId)
    {
        return $this->eventClient->saveProductData(
            $productId,
            $this->getProductCategoryCollection($productId)
        );
    }

    /**
     * Mark product as exported in DB
     *
     * @param $exportId
     */
    private function setProductExported($exportId)
    {
        $export = $this->exportFactory->create()->load($exportId);
        $export->setData('is_exported', '1');
        try {
            $export->save();
        } catch (\Exception $e) {
            $this->logger->addCritical($e->getMessage());
        }
    }

}
