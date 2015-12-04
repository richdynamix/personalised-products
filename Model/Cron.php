<?php
namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EventClient\Client;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\Collection as ExportCollection;
use \Richdynamix\PersonalisedProducts\Model\ResourceModel\Export\CollectionFactory;
use \Richdynamix\PersonalisedProducts\Model\ExportFactory;
use \Richdynamix\PersonalisedProducts\Api\Data\ExportInterface;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;

/**
 * Class Cron
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Cron
{
    /**
     * @var array
     */
    protected $_exportedProducts = [];

    /**
     * @var Client
     */
    protected $_eventClient;

    /**
     * @var ExportCollection
     */
    protected $_exportCollection;

    /**
     * @var CollectionFactory
     */
    protected $_exportCollectionFactory;

    /**
     * @var \Richdynamix\PersonalisedProducts\Model\ExportFactory
     */
    protected $_exportFactory;

    /**
     * @var PersonalisedProductsLogger
     */
    protected $_logger;

    /**
     * Cron constructor.
     * @param Client $client
     * @param ExportCollection $exportCollection
     * @param CollectionFactory $exportCollectionFactory
     * @param PersonalisedProductsLogger $logger
     */
    public function __construct(
        Client $client,
        ExportCollection $exportCollection,
        CollectionFactory $exportCollectionFactory,
        CollectionFactory $exportCollectionFactory,
        ExportFactory $_exportFactory,
        PersonalisedProductsLogger $logger
    )
    {
        $this->_eventClient = $client;
        $this->_exportCollection = $exportCollection;
        $this->_exportCollectionFactory = $exportCollectionFactory;
        $this->_exportFactory = $_exportFactory;
        $this->_logger = $logger;
    }

    /**
     * Method called on the cron to do the export
     *
     * @return bool
     */
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

    /**
     * Get product and category collections for export
     *
     * @return array
     */
    private function _getProductsForExport()
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

    /**
     * Set a list of product successfully exported
     *
     * @param $productId
     */
    private function _setExportedProducts($productId)
    {
        $this->_exportedProducts[] = $productId;
    }

    /**
     * Getter for the exportedProducts to be updated
     *
     * @return array
     */
    private function _getExportedProducts()
    {
        return $this->_exportedProducts;
    }

    /**
     * Update the database row with the `is_exported` as 1
     */
    private function _updateDatabase()
    {
        foreach ($this->_getExportedProducts() as $productId) {
            $model = $this->_getItemModel($productId);
            $model->setData('is_exported', '1');
            try {
                $model->save();
            } catch(\Exception $e) {
                $this->_logger->addCritical($e->getMessage());
            }

        }
    }

    /**
     * Load the item model by using product ID as identifier.
     *
     * //todo move this into a proper loadByProductId method on the model.
     *
     * @param $productId
     * @return $this
     */
    private function _getItemModel($productId)
    {
        $item = $this->_exportFactory->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)->getFirstItem();

        return $this->_exportFactory->create()->load($item->getId());
    }

}
