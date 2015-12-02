<?php
namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\Collection as ExportCollection;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\CollectionFactory;
use \Richdynamix\PersonalisedProducts\Api\Data\ExportInterface;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;

class Cron
{

    protected $_exportedProducts = [];
    protected $_eventClient;
    protected $_exportCollection;
    protected $_exportCollectionFactory;
    protected $_logger;

    public function __construct(
        Client $client,
        ExportCollection $exportCollection,
        CollectionFactory $exportCollectionFactory,
        PersonalisedProductsLogger $logger
    )
    {
        $this->_eventClient = $client;
        $this->_exportCollection = $exportCollection;
        $this->_exportCollectionFactory = $exportCollectionFactory;
        $this->_logger = $logger;
    }

    public function export()
    {
        $productExport = $this->_getProductsForExport();
        $productCount = count($productExport);
        $exportCount = 0;

        foreach ($productExport as $productId => $categories) {
            try {
                $this->_eventClient->saveProductData($productId, $categories);
                $this->_setExportedProducts($productId);
                ++$exportCount;
            } catch (\Exception $e) {
                $this->_logger->addCritical("Product ID - $productId failed to export: " . $e);
                return false;
            }
        }

        $this->_updateDatabase();

        $this->_logger->addInfo("Successfully exported " . $exportCount . " out " . $productCount . " products ");
        return true;
    }

    protected function _getProductsForExport()
    {
        $productExport = $this->_exportCollectionFactory
            ->create()
            ->addFieldToFilter('is_exported', '0')
            ->addOrder(
                ExportInterface::CREATION_TIME,
                ExportCollection::SORT_ORDER_DESC
            );

        $products = [];
        foreach ($productExport as $export) {
            $products[$export->getProductId()] = $export->getCategoryIds();
        }

        return $products;

    }

    protected function _setExportedProducts($productId)
    {
        $this->_exportedProducts[] = $productId;
    }

    protected function _updateDatabase()
    {
        // todo update all exported products as `is_exported`
    }
    
}