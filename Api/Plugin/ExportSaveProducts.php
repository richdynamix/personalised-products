<?php

namespace Richdynamix\PersonalisedProducts\Api\Plugin;

use Magento\Catalog\Model\Product;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Event\ManagerInterface;
use \Richdynamix\PersonalisedProducts\Model\Export;
use \Richdynamix\PersonalisedProducts\Model\ExportFactory;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;

/**
 * Class ExportSaveProducts
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class ExportSaveProducts
{
    /**
     * @var PersonalisedProductsLogger
     */
    private $logger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var ExportFactory
     */
    private $exportFactory;

    /**
     * ExportSaveProducts constructor.
     * @param PersonalisedProductsLogger $logger
     * @param Config $config
     * @param Export $export
     * @param ObjectManagerInterface $objectManager
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        PersonalisedProductsLogger $logger,
        Config $config,
        Export $export,
        ExportFactory $exportFactory,
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManager
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->export = $export;
        $this->exportFactory = $exportFactory;
        $this->objectManager = $objectManager;
        $this->eventManager = $eventManager;
    }

    /**
     * Plugin into the afterSave method on a product
     *
     * @param Product $product
     * @param $result
     * @return mixed
     */
    public function afterAfterSave(Product $product, $result)
    {
        $this->saveProductForExport($product->getId());
        return $result;
    }

    /**
     * Save the new product ready for export
     *
     * @param $productId
     */
    private function saveProductForExport($productId)
    {
        if (!$this->isReadyForExport($productId)) {
            $exportItem = $this->export->saveProductForExport($productId);

            $this->eventManager->dispatch(
                'personalised_products_export_after_save',
                ['exportItem' => $exportItem]
            );
        }
    }

    /**
     * Check the product exists in the export table
     *
     * @param $productId
     * @return bool
     */
    private function isReadyForExport($productId)
    {
        $product = $this->export->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem();

        return ($product->getData("product_id")) ? true : false;
    }
}
