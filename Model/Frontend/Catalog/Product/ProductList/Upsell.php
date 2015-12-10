<?php

namespace Richdynamix\PersonalisedProducts\Model\Frontend\Catalog\Product\ProductList;

use \Richdynamix\PersonalisedProducts\Model\PredictionIO\EngineClient\Similarity;
use \Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Upsell
 *
 * @category Richdynamix
 * @package  PersonalisedProducts
 * @author   Steven Richardson (steven@richdynamix.com) @mage_gizmo
 */
class Upsell
{
    /**
     * @var Similarity
     */
    private $similarityEngine;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Upsell constructor.
     * @param Similarity $similarityEngine
     * @param CustomerSession $customerSession
     */
    public function __construct(Similarity $similarityEngine, CustomerSession $customerSession)
    {
        $this->similarityEngine = $similarityEngine;
        $this->customerSession = $customerSession;
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
        $products = $this->similarityEngine->sendQuery($productIds, $categoryIds);

        if ($products['itemScores']) {
            return $this->getProductIds($products['itemScores']);
        }
        
        return false;

    }

    /**
     * Build product ID collection array from PredictionIO engine data
     *
     * @param $items
     * @return array
     */
    private function getProductIds($items)
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item['item'];
        }

        return $productIds;
    }
}
