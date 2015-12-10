<?php

// @codingStandardsIgnoreFile

namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Api\Data\ExportInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Richdynamix\PersonalisedProducts\Model\ExportFactory;
use \Richdynamix\PersonalisedProducts\Logger\PersonalisedProductsLogger;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Export
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Export extends AbstractModel implements ExportInterface
{
    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var \Richdynamix\PersonalisedProducts\Model\ExportFactory
     */
    private $exportFactory;

    /**
     * @var PersonalisedProductsLogger
     */
    protected $logger;

    /**
     * Export constructor.
     * @param ProductFactory $productFactory
     * @param Context $context
     * @param Registry $registry
     * @param \Richdynamix\PersonalisedProducts\Model\ExportFactory $exportFactory
     * @param PersonalisedProductsLogger $logger
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ProductFactory $productFactory,
        Context $context,
        Registry $registry,
        ExportFactory $exportFactory,
        PersonalisedProductsLogger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->productFactory = $productFactory;
        $this->exportFactory = $exportFactory;
        $this->logger = $logger;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Export constructor
     */
    protected function _construct()
    {
        $this->_init('Richdynamix\PersonalisedProducts\Model\ResourceModel\Export');
    }

    /**
     * Get table increment ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * Get product ID
     *
     * @return mixed
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Get created time
     *
     * @return mixed
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get updated time
     *
     * @return mixed
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Check product is exported
     *
     * @return bool
     */
    public function isExported()
    {
        return (bool) $this->getData(self::IS_EXPORTED);
    }

    /**
     * Set table row increment ID
     *
     * @param mixed $incrementId
     * @return $this
     */
    public function setId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * Set row product ID
     *
     * @param $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Set row created time
     *
     * @param $creationTime
     * @return $this
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Set row updated time
     *
     * @param $updatedTime
     * @return $this
     */
    public function setUpdateTime($updatedTime)
    {
        return $this->setData(self::UPDATE_TIME, $updatedTime);
    }

    /**
     * Set product as being exported
     *
     * @param $isExported
     * @return $this
     */
    public function setIsExported($isExported)
    {
        return $this->setData(self::IS_ACTIVE, $isExported);
    }

    /**
     * Get category ID's of the product
     *
     * @return array
     */
    public function getCategoryIds()
    {
        $product = $this->productFactory->create();
        $product->load($this->getData(self::PRODUCT_ID));

        return $product->getCategoryIds();
    }

    /**
     * Save the new export item with Product Id
     *
     * @param $productId
     * @return Export
     */
    public function saveProductForExport($productId)
    {
        $exportItem = $this->exportFactory->create();
        $exportItem->setData('product_id', $productId);
        try {
            $exportItem->save();
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
        }

        return $exportItem;
    }
}
