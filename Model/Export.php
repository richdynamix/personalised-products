<?php

namespace Richdynamix\PersonalisedProducts\Model;

use \Richdynamix\PersonalisedProducts\Api\Data\ExportInterface;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;

class Export extends AbstractModel implements ExportInterface
{
    public function __construct(
        ProductFactory $productFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_productFactory = $productFactory;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    protected function _construct()
    {
        $this->_init('Richdynamix\PersonalisedProducts\Model\ResourceModel\Export');
    }

    public function getId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    public function isExported()
    {
        return (bool) $this->getData(self::IS_EXPORTED);
    }

    public function setId($id)
    {
        return $this->setData(self::INCREMENT_ID, $id);
    }

    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    public function setUpdateTime($updatedTime)
    {
        return $this->setData(self::UPDATE_TIME, $updatedTime);
    }

    public function setIsExported($isExported)
    {
        return $this->setData(self::IS_ACTIVE, $isExported);
    }

    public function getCategoryIds()
    {
        $product = $this->_productFactory->create();
        $product->load($this->getData(self::PRODUCT_ID));

        return $product->getCategoryIds();
    }

}