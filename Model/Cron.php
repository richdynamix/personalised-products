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
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Cron
{
    /**
     * @var array
     */
    private $exportedProducts = [];

    /**
     * @var Client
     */
    private $eventClient;

    /**
     * @var ExportCollection
     */
    private $exportCollection;

    /**
     * @var CollectionFactory
     */
    protected $ecFactory;

    /**
     * @var \Richdynamix\PersonalisedProducts\Model\ExportFactory
     */
    private $exportFactory;

    /**
     * Cron constructor.
     * @param Client $client
     * @param ExportCollection $exportCollection
     * @param CollectionFactory $ecFactory
     * @param \Richdynamix\PersonalisedProducts\Model\ExportFactory $_exportFactory
     * @param PersonalisedProductsLogger $logger
     */
    public function __construct(
        Client $client,
        ExportCollection $exportCollection,
        CollectionFactory $ecFactory,
        ExportFactory $_exportFactory,
        PersonalisedProductsLogger $logger
    ) {
        $this->eventClient = $client;
        $this->exportCollection = $exportCollection;
        $this->ecFactory = $ecFactory;
        $this->exportFactory = $_exportFactory;
        $this->logger = $logger;
    }

    /**
     * @var PersonalisedProductsLogger
     */
    private $logger;

    /**
     * Method called on the cron to do the export
     *
     * @return bool
     */
    public function export()
    {
        $productExport = $this->getProductsForExport();
        $productCount = count($productExport);
        $exportCount = 0;

        foreach ($productExport as $productId => $categories) {
            try {
                $this->eventClient->saveProductData($productId, $categories);
                $this->setExportedProducts($productId);
                ++$exportCount;
            } catch (\Exception $e) {
                $this->logger->addCritical("Product ID - $productId failed to export: " . $e);
                return false;
            }
        }

        $this->updateDatabase();

        $this->logger->addInfo("Successfully exported " . $exportCount . " out " . $productCount . " products ");
        return true;
    }

    /**
     * Get product and category collections for export
     *
     * @return array
     */
    private function getProductsForExport()
    {
        $productExport = $this->ecFactory
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
    private function setExportedProducts($productId)
    {
        $this->exportedProducts[] = $productId;
    }

    /**
     * Getter for the exportedProducts to be updated
     *
     * @return array
     */
    private function getExportedProducts()
    {
        return $this->exportedProducts;
    }

    /**
     * Update the database row with the `is_exported` as 1
     */
    private function updateDatabase()
    {
        foreach ($this->getExportedProducts() as $productId) {
            $model = $this->getItemModel($productId);
            $model->setData('is_exported', '1');
            try {
                $model->save();
            } catch (\Exception $e) {
                $this->logger->addCritical($e->getMessage());
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
    private function getItemModel($productId)
    {
        $item = $this->exportFactory->create()->getCollection()
            ->addFieldToFilter('product_id', $productId)->getFirstItem();

        return $this->exportFactory->create()->load($item->getId());
    }
}
