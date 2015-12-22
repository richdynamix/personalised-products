<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList;

use Richdynamix\PersonalisedProducts\Helper\Config;
use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Similarity;
use \Magento\Customer\Model\Session as CustomerSession;
use \Magento\Catalog\Model\Product\Visibility as Visibility;
use \Magento\Catalog\Model\ProductFactory as ProductFactory;

/**
 * Class Upsell
 *
 * @category  Richdynamix
 * @package   PersonalisedProducts
 * @author    Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Upsell
{
    /**
     * @var Similarity
     */
    private $_similarityEngine;

    /**
     * @var CustomerSession
     */
    private $_customerSession;

    /**
     * @var Config
     */
    private $_config;

    /**
     * Upsell constructor.
     * @param Similarity $similarityEngine
     * @param CustomerSession $customerSession
     * @param Config $config
     * @param Visibility $productVisibility
     * @param ProductFactory $productFactory
     */
    public function __construct(
        Similarity $similarityEngine,
        CustomerSession $customerSession,
        Config $config,
        Visibility $productVisibility,
        ProductFactory $productFactory
    ) {
        $this->_similarityEngine = $similarityEngine;
        $this->_customerSession = $customerSession;
        $this->_config = $config;
        $this->_productFactory = $productFactory;
    }

    /**
     * Query the PredictionIO engine for product data
     *
     * @param array $productIds
     * @param $categoryIds
     * @return array|bool
     */
    public function getProductCollection(array $productIds, $categoryIds)
    {
        $products = $this->_similarityEngine->sendQuery($productIds, $categoryIds);

        if ($products['itemScores']) {
            return $this->_getProductIds($products['itemScores']);
        }
        
        return false;
    }

    /**
     * Build product ID collection array from PredictionIO engine data
     *
     * @param array $items
     * @return array
     */
    private function _getProductIds(array $items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item['item'];
        }

        return $productIds;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getCategoryIds(\Magento\Catalog\Model\Product $product)
    {
        if (!$this->_config->getItem(Config::SIMILARITY_USE_CATEGORY_FILTER)) {
            return [];
        }

        return $product->getCategoryIds();

    }

    /**
     * Get a new product collection from prediction IO result set
     *
     * @param array $personalisedIds
     * @return $this
     */
    public function getPersonalisedProductCollection(array $personalisedIds)
    {
        $collection = $this->_productFactory->create()->getCollection()
            ->addAttributeToFilter('entity_id', ['in', $personalisedIds])
            ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
            ->addAttributeToFilter('status', array('eq' => 1));
        return $collection;
    }
}
