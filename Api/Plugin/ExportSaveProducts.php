<?php

namespace Richdynamix\PersonalisedProducts\Api\Plugin;

use Magento\Catalog\Model\Product;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\Event\ManagerInterface;
use Richdynamix\PersonalisedProducts\Model\Export;
use \Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;

/**
 * Class ExportSaveProducts
 *
 * @category    Richdynamix
 * @package     PersonalisedProducts
 * @author 		Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class ExportSaveProducts
{
    /**
     * @var PersonalisedProductsLogger
     */
    private $_logger;
    /**
     * @var Config
     */
    private $_config;
    /**
     * @var Export
     */
    private $_export;

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
        ObjectManagerInterface $objectManager,
        ManagerInterface $eventManager
    )
    {
        $this->_logger = $logger;
        $this->_config = $config;
        $this->_export = $export;
        $this->_objectManager = $objectManager;
        $this->_eventManager = $eventManager;
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
        $this->_saveProductForExport($product->getId());
        return $result;
    }

    /**
     * Save the new product ready for export
     *
     * @param $productId
     */
    private function _saveProductForExport($productId)
    {
        if (!$this->_isReadyForExport($productId)) {
            $model = $this->_objectManager->create('Richdynamix\PersonalisedProducts\Model\Export');
            $model->setData('product_id', $productId);
            try {
                $model->save();
            } catch(\Exception $e) {
                $this->_logger->addError($e->getMessage());
            }

            $this->_eventManager->dispatch(
                'personalised_products_export_after_save',
                ['product' => $model]
            );
        }
    }


    /**
     * Check the product exists in the export table
     *
     * @param $productId
     * @return bool
     */
    private function _isReadyForExport($productId)
    {
        $product = $this->_export->getCollection()
            ->addFieldToFilter('product_id', $productId)
            ->getFirstItem();

        return ($product->getData("product_id")) ? true : false;
    }
}
