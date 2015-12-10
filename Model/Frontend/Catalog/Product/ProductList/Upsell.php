<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Similarity;
use \Magento\Customer\Model\Session as CustomerSession;

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
     * Upsell constructor.
     * @param Similarity $similarityEngine
     * @param CustomerSession $customerSession
     */
    public function __construct(Similarity $similarityEngine, CustomerSession $customerSession)
    {
        $this->_similarityEngine = $similarityEngine;
        $this->_customerSession = $customerSession;
    }

    /**
     * Query the PredictionIO engine for product data
     *
     * @param $productIds
     * @param $categoryIds
     * @return array|bool
     */
    public function getProductCollection($productIds, $categoryIds)
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
     * @param $items
     * @return array
     */
    private function _getProductIds($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item['item'];
        }

        return $productIds;
    }
}
