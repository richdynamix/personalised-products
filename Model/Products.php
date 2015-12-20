<?php

namespace Richdynamix\PersonalisedProducts\Model;

use \Magento\Catalog\Model\ProductFactory as ProductFactory;
use \Magento\Catalog\Model\Product\Visibility as Visibility;

/**
 * Loads product collections from predicted product ids
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Products
{
    /**
     * @var ProductFactory
     */
    private $_productFactory;

    /**
     * Products constructor.
     * @param ProductFactory $productFactory
     * @param Visibility $productVisibility
     */
    public function __construct(ProductFactory $productFactory, Visibility $productVisibility)
    {
        $this->_productFactory = $productFactory;
    }

    /**
     * We only want to show visible and enabled products.
     *
     * @param $personalisedIds
     * @return $this
     */
    public function getPersonalisedProductCollection($personalisedIds)
    {
        $collection = $this->_productFactory->create()->getCollection()
            ->addAttributeToFilter('entity_id', ['in', $personalisedIds])
            ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', array('eq' => 1));
        return $collection;
    }
}